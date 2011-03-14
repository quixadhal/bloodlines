#include <lib.h>
#include <daemons.h>
#include <talk_type.h>

inherit LIB_VERB;

static void create() {
    verb::create();
    SetVerb("channels");
    SetRules("", "STR");
    SetHelp("Syntax: channels [on | off]\n\n"
            "With no argument this command will display the status "
            "of the lines to which you have access.  With the argument on|off "
            "it will turn all of the lines on or off.\n"
            "See also: chan, mute, gag, earmuff, env");
    SetSynonyms(({"chans","lines"}));
}

int cmd(string str);

mixed can_channels(string str) { return 1; }

mixed can_channels_str(string str) { return 1; }

mixed do_channels(string str) {
    return cmd(str);
}

mixed do_channels_str(string str) {
    return cmd(str);
}

int cmd(string str) {
    string *channels;
    mapping allchannels;
    string *allimc2channels;
    string *remote = ({});
    string *local = ({});
    string *i3 = ({});
    string *imc2 = ({});
    string ret = "", RemChans = CHAT_D->GetRemoteChannels();
    int i;

    channels = distinct_array(this_player()->GetChannels());
    allchannels = INTERMUD_D->GetChannelList();
    allimc2channels = IMC2_D->GetChanList();
    foreach(string k in keys(allchannels)){
        remote += ({ k });
        allchannels[k] += ({ " I3 " });
        //i3 += ({ k });
    }
    foreach(string k in allimc2channels){
        array d = explode(k, ":");
        string name = d[1];
        string server = d[0];

        allchannels[name] = ({ server, 0, "IMC2" });
        remote += ({ name });
        //imc2 += ({ name });
    }

    if(!str) str = "";

    if(str=="on"){
        for(i=0; i<sizeof(channels);i++){
            if(this_player()->GetBlocked(channels[i]))
                this_player()->SetBlocked(channels[i]);
        }
    }

    if(str=="off"){
        for(i=0; i<sizeof(channels);i++){
            if(!this_player()->GetBlocked(channels[i]))
                this_player()->SetBlocked(channels[i]);
        }
    }

    foreach(string chan in channels){
        string tmp = CHAT_D->GetRemoteChannel(chan);
        if(member_array(tmp, RemChans) != -1){
            remote += ({ chan });
            if(!strsrch(tmp, "Server") && grepp(tmp, ":")){
                imc2 += ({ chan });
            }
            else i3 += ({ chan });
        }
        else local += ({ chan });
    }

    remote = distinct_array(remote);
    i3 = distinct_array(i3);
    imc2 = distinct_array(imc2);

    if(str=="list"){
        ret += "ALL CHANNELS:\n";
        foreach(string k in sort_array(keys(allchannels), 1)){
            if(member_array(k, imc2) != -1) {
                ret += "%^GREEN%^";
                ret += "  IMC2  ";
            } else if(member_array(k, i3) != -1) {
                ret += "%^GREEN%^";
                ret += "   I3   ";
            } else if(member_array(k, local) != -1) {
                ret += "%^GREEN%^";
                ret += "  LOCAL ";
            } else {
                ret += "%^RED%^";
                ret += "- " + allchannels[k][2] + " ";
            }
            ret += k + " @ " + allchannels[k][0] + "\n";
            ret += "%^RESET%^";
        }
        ret += "\n";
        this_player()->eventPage( explode(ret, "\n") );
        //write(ret);
        return 1;
    }

    if(sizeof(remote)){
        ret += "REMOTE CHANNELS\n---------------\n";
        if(sizeof(imc2)){
            imc2 = sort_array(imc2, 1);
            ret += "\nIMC2\n----\n";
            foreach(string chan in imc2){ 
                ret += chan + "\t" + (this_player()->GetBlocked(chan) ?
                        "(%^RED%^BLOCKED%^RESET%^)" : "") + "\n";
            }
        }
        if(sizeof(i3)){
            i3 = sort_array(i3, 1);
            ret += "\nIntermud-3\n----------\n";
            foreach(string chan in i3){ 
                ret += chan + "\t" + (this_player()->GetBlocked(chan) ?
                        "(%^RED%^BLOCKED%^RESET%^)" : "") + "\n";
            }
        }
    }
    if(sizeof(local)){
        local = sort_array(local, 1);
        ret += "\n\nLOCAL CHANNELS\n---------------\n";
        foreach(string chan in local){
            ret += chan + "\t" + (this_player()->GetBlocked(chan) ?
                    "(%^RED%^BLOCKED%^RESET%^)" : "") + "\n";
        }
    }

    if(!sizeof(ret)){
        ret = "No channels found.";
    }
    write(ret);
    return 1;
}		
