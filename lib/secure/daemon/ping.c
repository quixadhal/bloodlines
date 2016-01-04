#include <lib.h>
#include ROOMS_H
#include <daemons.h>

inherit LIB_DAEMON;

int Pinging = 0;
int OK = 0;
int Retries = 0;
int counter = 0;
int last_time = time();
string start_of_downtime = "";

string *muds = PINGING_MUDS + ({ mud_name() });

// Yes, I know... naughty.

static int LogIt(string what, string where, string canale){
//    if( (member_array(canale,local_chans) != -1 && LOG_LOCAL_CHANS) ||
//            ( member_array(GetRemoteChannel(canale),remote_chans) != -1 && LOG_REMOTE_CHANS) ){
        unguarded( (: write_file($(where), $(what)) :) );
        return 1;
//    }
//    else return 0;
}

int CheckOK(){
    string list = load_object("/cmds/players/mudlist")->cmd("");
    string logmsg = "";
    string the_time = "";
    string server = "";
    Pinging = 0;
    if(DISABLE_INTERMUD) return 1;
    if(!(INTERMUD_D->GetConnectedStatus())){
        OK = 0;
    }
    if(!OK){
        Retries++;
        if(Retries > 3) {
            the_time = CHAT_D->getColorDayTime();
            server = INTERMUD_D->GetNameserver();
            if(start_of_downtime != "") {
                tell_room(ROOM_ARCH,the_time+" The Arch Room loudspeaker announces: \""+server+" has been down since "+start_of_downtime+".\"");
                logmsg = sprintf("%s%03d\t%s\t%s\t%s\n", timestamp(), 0, "intercre", "CHAT_D@" + mud_name(), server+" has been down since "+start_of_downtime+".");
                LogIt(logmsg, "/secure/log/allchan.log", "intercre");
            }
            server = INTERMUD_D->NextNameserver();
            tell_room(ROOM_ARCH,the_time+" The Arch Room loudspeaker continues: \"Switching Intermud to "+server+".\"");
            logmsg = sprintf("%s%03d\t%s\t%s\t%s\n", timestamp(), 0, "intercre", "CHAT_D@" + mud_name(), "Switching Intermud to "+server+".");
            LogIt(logmsg, "/secure/log/allchan.log", "intercre");
            start_of_downtime = "";
        }
        update(INTERMUD_D);
    } else {
        if(Retries > 0 && INTERMUD_D->GetConnectedStatus()){
            the_time = CHAT_D->getColorDayTime();
            server = INTERMUD_D->GetNameserver();
            tell_room(ROOM_ARCH,the_time+" The Arch Room loudspeaker announces: \"%^BOLD%^CYAN%^"
                "Intermud connection is %^BOLD%^GREEN%^ONLINE%^BOLD%^CYAN%^.%^RESET%^\"");
            if(start_of_downtime != "") {
                tell_room(ROOM_ARCH,the_time+" The Arch Room loudspeaker continues: \""+server+" has been down since "+start_of_downtime+".\"");
            }
            logmsg = sprintf("%s%03d\t%s\t%s\t%s\n", timestamp(), 0, "intercre", "CHAT_D@" + mud_name(), "Intermud connection ("+server+") is ONLINE.");
            LogIt(logmsg, "/secure/log/allchan.log", "intercre");
            if(start_of_downtime != "") {
                logmsg = sprintf("%s%03d\t%s\t%s\t%s\n", timestamp(), 0, "intercre", "CHAT_D@" + mud_name(), server+" has been down since "+start_of_downtime+".");
                LogIt(logmsg, "/secure/log/allchan.log", "intercre");
            }
            start_of_downtime = "";
            load_object(ROOM_ARCH)->SetImud(1);
            // SERVICES_D->eventSendChannel(name, rc, str, emote, convert_name(targetkey), target_msg);;
            // SERVICES_D->eventSendChannel("CHAT_D", "intercre", "The Arch Room loudspeaker announces: %^BOLD%^CYAN%^Intermud connection is back %^BOLD%^GREEN%^ONLINE%^BOLD%^CYAN%^.%^RESET%^", 0);
        }
        Retries = 0;
    }
    if(Retries == 2 && !(INTERMUD_D->GetConnectedStatus())){
        the_time = CHAT_D->getDayTime();
        start_of_downtime = the_time;
        server = INTERMUD_D->GetNameserver();
        tell_room(ROOM_ARCH,the_time+" The Arch Room loudspeaker announces: \"%^BOLD%^CYAN%^"
                "Intermud connection ("+server+") is %^BOLD%^RED%^OFFLINE%^BOLD%^CYAN%^.%^RESET%^\"");
        logmsg = sprintf("%s%03d\t%s\t%s\t%s\n", timestamp(), 0, "intercre", "CHAT_D@" + mud_name(), "Intermud connection ("+server+") is OFFLINE.");
        LogIt(logmsg, "/secure/log/allchan.log", "intercre");
        rm("/tmp/muds.txt");
        load_object(ROOM_ARCH)->SetImud(0);
    }
    write_file("/www/mudlist.txt",timestamp()+"\n",1);
    write_file("/www/mudlist.txt",""+list);
    return 1;
}

int eventPing(){
    mixed chans;
    if(DISABLE_INTERMUD) return 1;
    chans = INTERMUD_D->GetChannels();
    Pinging = 1;
    OK = 0;
    if(!sizeof(muds)) {
        Pinging = 0;
        return 0;
    }
    foreach(string mud in muds){
        INTERMUD_D->eventWrite(({ "auth-mud-req", 5, mud_name(), 0, mud, 0 }));
    }
    if(!sizeof(chans)){
        string rtr = INTERMUD_D->GetNameservers()[0][0];
        INTERMUD_D->eventWrite(({ "chanlist-req", 5, mud_name(), 0, rtr, 0 }));
    }
    return 1;
}

void create() {
    daemon::create();
    SetNoClean(1);
    if(!DISABLE_INTERMUD) set_heart_beat(1);
    else {
        set_heart_beat(0);
        Pinging = 0;
    }
}

void DeadMan(){
    //    This breaks things.
    //    int tmptime = time();
    //    if(last_time + 60 < tmptime){
    //	object *clones = filter(objects(), (: inherits(LIB_ROOM,$1) :));
    //	clones->eventDestruct();
    //
    //	clones = filter(objects(), (: clonep($1) :));
    //	clones->eventDestruct();
    //    }
    //    else last_time = tmptime;
}

void heart_beat(){
    counter++;
    DeadMan();
    if(!DISABLE_INTERMUD){
        if(!(counter % (PING_INTERVAL + 2))) CheckOK();
        if(!(counter % (PING_INTERVAL))) eventPing();
    }
    if(counter > (PING_INTERVAL * 1000)) counter = 0;
}

int GetPinging(){
    return Pinging;
}

int SetOK(){
    if(DISABLE_INTERMUD) return 1;
    OK = 1;
    load_object(ROOM_ARCH)->SetImud(1);
}
