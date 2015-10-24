/*    /daemon/chat.c
 *    from the Dead Souls Mudlib
 *    daemon to handle all mud chat channels
 *    created by Descartes of Borg 931220
 *
 *    IMC2 support added by Shadyman 2007-Sep-24
 *    Feelings support added by Shadyman 2007-Sep-24
 *    "mapping tags" added by Shadyman 2007-Sep-24
 */

#ifndef LOG_REMOTE_CHANS
#define LOG_REMOTE_CHANS 1
#endif

#ifndef LOG_LOCAL_CHANS
#define LOG_LOCAL_CHANS 1
#endif

#ifndef CHANNEL_PIPES
#define  CHANNEL_PIPES 1
#endif

#include <privs.h>
#include <lib.h>
#include <save.h>
#include <pov.h>
#include <daemons.h>
#include <origin.h>
#include <message_class.h>
#include "include/chat.h"

inherit LIB_DAEMON;

mapping chatters = ([]);
int chat_counter = 0;
static private mapping hour_colors = ([
        "00" : "%^DARKGREY%^",
        "01" : "%^DARKGREY%^",
        "02" : "%^DARKGREY%^",
        "03" : "%^DARKGREY%^",
        "04" : "%^RED%^",
        "05" : "%^RED%^",
        "06" : "%^ORANGE%^",
        "07" : "%^ORANGE%^",
        "08" : "%^YELLOW%^",
        "09" : "%^YELLOW%^",
        "10" : "%^GREEN%^",
        "11" : "%^GREEN%^",
        "12" : "%^LIGHTGREEN%^",
        "13" : "%^LIGHTGREEN%^",
        "14" : "%^WHITE%^",
        "15" : "%^WHITE%^",
        "16" : "%^LIGHTCYAN%^",
        "17" : "%^LIGHTCYAN%^",
        "18" : "%^CYAN%^",
        "19" : "%^CYAN%^",
        "20" : "%^LIGHTBLUE%^",
        "21" : "%^LIGHTBLUE%^",
        "22" : "%^BLUE%^",
        "23" : "%^BLUE%^",
        ]);

static private array chat_colors = ({
            "%^RED%^",
            "%^GREEN%^",
            "%^ORANGE%^",
            "%^BLUE%^",
            "%^MAGENTA%^",
            "%^CYAN%^",
            "%^DARKGREY%^",
            "%^LIGHTRED%^",
            "%^LIGHTGREEN%^",
            "%^YELLOW%^",
            "%^LIGHTBLUE%^",
            "%^PINK%^",
            "%^LIGHTCYAN%^",
            "%^WHITE%^",

            "%^B_RED%^%^WHITE%^",
            "%^B_GREEN%^%^WHITE%^",
            "%^B_BLUE%^%^WHITE%^",
            "%^B_MAGENTA%^%^WHITE%^",

            "%^B_RED%^%^YELLOW%^",
            "%^B_GREEN%^%^YELLOW%^",
            "%^B_BLUE%^%^YELLOW%^",
            "%^B_MAGENTA%^%^YELLOW%^",

            "%^B_RED%^%^BLACK%^",
            "%^B_GREEN%^%^BLACK%^",
            "%^B_MAGENTA%^%^BLACK%^",
            "%^B_CYAN%^%^BLACK%^",
            "%^B_YELLOW%^%^BLACK%^",
            "%^B_WHITE%^%^BLACK%^",

            "%^B_CYAN%^%^BLUE%^",
            "%^B_YELLOW%^%^BLUE%^",
            "%^B_WHITE%^%^BLUE%^",

            "%^B_YELLOW%^%^GREEN%^",
            "%^B_WHITE%^%^GREEN%^",
        });

static string SaveFile;

static string chan;
static private mapping Channels;
static private mapping chanlast;

static private string *local_chans = ({});
static private string *remote_chans = ({});
static string *syschans = ({});

static private mapping localchans = ([
        //I3 Channels
        "imud_code": "intercre",
        "imud_gossip": "intergossip",
        "ie_flibcode": "foundation",
        "dead_test4": "ds_test",
        "dead_souls": "ds",

        //IMC2 Channels
        "Server02:igame": "i2game2",
        "Server02:inews": "i2news2",
        "Server01:ibuild": "ibuild2",
        "Server01:ichat": "mbchat",
        "Server01:pchat": "pchat2",
        "Server01:i2game": "i2game2",
        "Server01:i2chat": "i2chat2",
        "Server01:i3chat": "i3chat2",
        "Server01:i2code": "i2code2",
        "Server01:i2news": "i2news2",
        "Server01:imudnews": "imudnews2",
        "Server01:irc": "irc2",
        "Server01:ifree": "ifree2",

        ]);

        static private mapping remotechans = ([
                //I3 Channels
                "intercre": "imud_code",
                "intergossip": "imud_gossip",
                "foundation": "ie_flibcode",
                "dutch": "dutch",
                "ds_test": "dead_test4",
                "ds": "dead_souls",

                //IMC2 Channels
                "i2game2": "Server02:igame",
                "i2news2": "Server02:inews",
                "ibuild2": "Server01:ibuild",
                "mbchat": "Server01:ichat",
                "pchat2": "Server01:pchat",
                "i2game2": "Server01:i2game",
                "i2chat2": "Server01:i2chat",
                "i3chat2": "Server01:i3chat",
                "i2code2": "Server01:i2code",
                "i2news2": "Server01:i2news",
                "imudnews2": "Server01:imudnews",
                "irc2": "Server01:irc",
                "ifree2": "Server01:ifree",
                ]);

static private mapping tags = ([
        "intermud"    : "%^B_BLACK%^WHITE%^",
        "muds"        : "%^B_BLACK%^WHITE%^",
        "connections" : "%^B_BLACK%^BOLD%^WHITE%^",
        "death"       : "%^BOLD%^RED%^",
        "cre"         : "%^BOLD%^GREEN%^",
        "admin"       : "%^BOLD%^MAGENTA%^",
        "newbie"      : "%^BOLD%^B_YELLOW%^",
        "gossip"      : "%^BOLD%^B_BLUE%^",

        "ds"          : "%^YELLOW%^",
        "dchat"	      : "%^CYAN%^",
        "intergossip" : "%^GREEN%^",
        "intercre"    : "%^ORANGE%^",
        "pyom"        : "%^FLASH%^%^LIGHTGREEN%^",
        "free_speech" : "%^PINK%^",
        "url"         : "%^WHITE%^",

        "ibuild2"      : "%^B_RED%^%^YELLOW%^",
        "mbchat"       : "%^B_RED%^%^GREEN%^",
        "pchat2"       : "%^B_RED%^%^BOLD%^GREEN%^",
        "i2game2"      : "%^B_BLUE%^",
        "i2chat2"      : "%^B_GREEN%^",
        "i3chat2"      : "%^B_RED%^",
        "i2code2"      : "%^B_YELLOW%^%^RED%^",
        "i2news2"      : "%^B_YELLOW%^%^BLUE%^",
        "imudnews2"    : "%^B_YELLOW%^%^CYAN%^",
        "irc2"         : "%^B_BLUE%^%^GREEN%^",
        "ifree2"         : "%^B_BLUE%^%^GREEN%^",

        "default"     : "%^BOLD%^BLUE%^",
        "default-IMC2" : "%^BOLD%^WHITE%^%^B_BLUE%^",
        ]);

static void Setup(){
    mixed rchan2 = ({});
    mixed rchan3 = ({});
    remote_chans = ({});
    local_chans = ({"newbie","cre","gossip","admin","error", "intermud",
            "death", "connections", "muds" });
    syschans = ({ "intermud", "death", "connections", "muds" });

    local_chans += CLASSES_D->GetClasses();

    if(find_object(INTERMUD_D)){
        if(arrayp(INTERMUD_D->GetChannels()))
            rchan3 += distinct_array(INTERMUD_D->GetChannels());
    }
    if(find_object(IMC2_D)){
        if(arrayp(IMC2_D->GetChanList()))
            rchan2 += distinct_array(IMC2_D->GetChanList());
    }

    foreach(mixed foo in local_chans){
        if(!stringp(foo)){
            local_chans -= ({ foo });
        }
    }

    rchan3 = sort_array(filter(rchan3, (: stringp($1) :)), 1);
    rchan2 = sort_array(filter(rchan2, (: stringp($1) :)), 1);

    foreach(mixed bar in ({ rchan3, rchan2 })){
        foreach(mixed foo in bar){
            string svr, cnl;
            if(!stringp(foo) || member_array(foo, local_chans) != -1){
                bar -= ({ foo });
                continue;
            }
            if(sscanf(foo, "%s:%s", svr, cnl) == 2){
                if(member_array(cnl, local_chans) == -1 && !localchans[cnl] &&
                  member_array(cnl, rchan3) == -1){
                    localchans[foo] = cnl;
                    remotechans[cnl] = foo;
                }
            }
        }
    }
    remote_chans = distinct_array((rchan3 + rchan2));
    local_chans = distinct_array(local_chans);
}

static void create() {
    object pl;
    string *tmp_arr = ({});
    daemon::create();
    SetNoClean(1);
    Channels = ([]);

    SaveFile = save_file(SAVE_CHAT);
    if(unguarded((: file_exists(SaveFile) :))){
        RestoreObject(SaveFile);
    }
    SaveObject(SaveFile);

    call_out("Setup", 10);

    foreach(string kanal in local_chans + syschans){
        if( !Channels[kanal] ) Channels[kanal] = ({});
    }
    foreach(pl in users()) {
        string *chans;
        string channel;

        if( pl && !(chans = pl->GetChannels()) ) continue;
        foreach(channel in chans) {
            if( !Channels[channel] ) Channels[channel] = ({});
            Channels[channel] = distinct_array(Channels[channel] + ({ pl }));
        }
    }
    foreach( string channel in local_chans ){
        tmp_arr += ({ channel + "emote" });
        tmp_arr += ({ channel + ":" });
    }

    local_chans += tmp_arr;
}

string *AddRemoteChannel(mixed chan){
    string *ret = copy(remote_chans);
    if(base_name(previous_object()) != INTERMUD_D) return ret;
    if(stringp(chan)) chan = ({ chan });
    if(!arrayp(chan)) return ret;
    foreach(string element in chan){
        if(member_array(element, local_chans) != -1){
            chan -= ({ element });
        }
    }
    return copy(remote_chans = distinct_array(remote_chans += chan));
}

string *AddLocalChannel(mixed chan){
    string *ret = copy(local_chans);
    if(base_name(previous_object()) != PARTY_D) return ret;
    if(stringp(chan)) chan = ({ chan });
    if(!arrayp(chan)) return ret;
    foreach(string element in chan){
        if(member_array(element, local_chans) != -1){
            chan -= ({ element });
        }
    }
    return copy(local_chans = distinct_array(local_chans += chan));
}

string *RemoveRemoteChannel(mixed chan){
    string *ret = copy(remote_chans);
    if(base_name(previous_object()) != INTERMUD_D) return ret;
    if(stringp(chan)) chan = ({ chan });
    if(!arrayp(chan)) return ret;
    foreach(string element in chan){
        if(member_array(element, local_chans) != -1){
            chan -= ({ element });
        }
    }
    return copy(remote_chans = distinct_array(remote_chans -= chan));
}

string *RemoveLocalChannel(mixed chan){
    string *ret = copy(local_chans);
    if(base_name(previous_object()) != PARTY_D) return ret;
    if(stringp(chan)) chan = ({ chan });
    if(!arrayp(chan)) return ret;
    foreach(string element in chan){
        if(member_array(element, local_chans) != -1){
            chan -= ({ element });         }
    }     
    return copy(local_chans = distinct_array(local_chans -= chan));
}

varargs string *GetRemoteChannels(int localized){
    mixed *ret = ({});
    if(!localized) return copy(remote_chans);
    foreach(string chan in remote_chans){
        ret += ({ GetLocalChannel(chan) });
    }
    return ret;
}

string decolor(string str){
    string s1 = "", s2, s3, test;
    int tmp = 2;
    if(sscanf(str,"%s<%s>%s",s1,s2,s3) != 3)
        tmp = sscanf(str,"<%s>%s",s2,s3);
    if(tmp != 2) return str;
    else {
        //test = s1+"<"+s2+">%^RESET%^"+TERMINAL_D->no_colours(s3);
        test = s1+"<"+s2+">%^RESET%^"+strip_colours(s3);
        return test;
    }
}

varargs int CanListen(object who, string canal){
    if(!RESTRICTED_INTERMUD) return 1;
    if(canal && member_array(canal, local_chans) != -1) return 1;
    else return imud_privp(who);
}

varargs int CanTalk(object who, string canal){
    if(!RESTRICTED_INTERMUD) return 1;
    if(canal && member_array(canal, local_chans) != -1) return 1;
    else return imud_privp(who);
}

string *eventRegisterMember(string *chans) {
    string *tmp;
    object ob;
    string channel;

    if( !living(ob = previous_object()) ) return ({});
    tmp = ({});
    foreach(channel in chans) {
        /* just check out for secure channels */
        switch(channel) {
            case "admin":
                if( !archp(ob) ) break;
            case "cre": case "intercre": case "intergossip":
                if( !creatorp(ob) ) break;
            default:
            if( !Channels[channel]) Channels[channel] = ({});
            Channels[channel] = distinct_array(Channels[channel] + ({ ob }));
            tmp += ({ channel });
        }
    }
    return tmp;
}

string *eventRemoveMember(string *chans) {
    object ob;
    string channel;

    if( !living(ob = previous_object()) ) return({});
    foreach(channel in chans) {
        if( !Channels[channel] ) continue;
        else Channels[channel] -= ({ ob });
        if( !sizeof(Channels[channel]) ) map_delete(Channels, channel);
    }
    return chans;
}

int cmdLast(string feep){

    if(!chanlast||!Channels[feep]||member_array(this_player(), Channels[feep])==-1){

        this_player()->eventPrint("You are not subscribed to that channel.", MSG_ERROR);
        return 1;
    }
    if(!sizeof(chanlast[feep]))
    {
        this_player()->eventPrint("That channel has no backlog.", MSG_ERROR);
        return 1;
    }
    if(!CanListen(this_player(),feep)){
        write("You lack privileges to that channel.");
        return 1;
    }
    this_player()->eventPrint(implode(chanlast[feep], "\n"));
    return 1;
}

static int LogIt(string what, string where, string canale){
    if( (member_array(canale,local_chans) != -1 && LOG_LOCAL_CHANS) ||
            ( member_array(GetRemoteChannel(canale),remote_chans) != -1 && LOG_REMOTE_CHANS) ){
        unguarded( (: write_file($(where), $(what)) :) );
        return 1;
    }
    else return 0;
}

varargs int eventAddLast(string feep, string str, string pchan, string pmsg, string pwho)
{
    string plainmsg;
    string Chan=feep;
    if(!chanlast)
        chanlast=([]);
    if(!sizeof(chanlast[Chan]))
        chanlast[Chan] = ({});
    if(sizeof(chanlast[Chan]) == 50)
        chanlast[Chan] = chanlast[Chan][1..sizeof(chanlast[Chan])];
    chanlast[Chan] += ({ str });
    Chan = GetLocalChannel(Chan);

    if (Chan == "death") return 1;

    //Log in either SQL or file
#ifdef MYSQL
    if (MYSQL_D->sql_request("INSERT INTO LOG_CHAT (Channel,Who,What) VALUES (\'"+ escape(pchan) +"\',\'" + escape(pwho) + "\',\'" + escape(pmsg) + "\')") == 0) {
        true();
    }
#endif
    Chan = GetLocalChannel(Chan);
    if(!pchan || pchan == "") pchan = "foo";
    plainmsg = "bar";
    if(pchan) plainmsg = "<" + pchan + "> ";
    if(pmsg) plainmsg += pmsg;
    if(pwho && pwho !="") plainmsg = pwho+" "+plainmsg;
    if(pchan && pchan != "admin"){
        LogIt("["+timestamp()+"] "+plainmsg+"\n", "/log/chan/"+Chan, Chan);
    }
    else {
        LogIt("["+timestamp()+"] "+plainmsg+"\n", "/secure/log/"+Chan, Chan);
    }
    return 1;
}

string novowel(string str) {
    string ret = "";
    //ret = implode(filter(explode(str, ""), (: ($1 >= "A" && $1 <= "Z") || ($1 >= "a" && $1 <= "z" ) || ($1 >= "0" && $1 <= "9") ? 1 : 0 :)), "");
    ret = implode(filter(explode(str, ""), (: (member_array(lower_case($1), ({ "a", "e", "i", "o", "u" })) > -1) ? 0 : 1 :)), "");
    return ret;
}

string gnomish(string str) {
    string ret = "";
    array words = explode(str, " ");
    foreach( string word in words ) {
        ret += capitalize(lower_case(word));
    }
    ret = implode(filter(explode(ret, ""), (: ($1 >= "A" && $1 <= "Z") || ($1 >= "a" && $1 <= "z" ) || ($1 >= "0" && $1 <= "9") ? 1 : 0 :)), "");
    return ret;
}

string boldify(string str, string color) {
    string ret = "";
    int counter = 0;

    if( strlen(str) < 1 )
        return ret;

    foreach(mixed element in str) {
        if(counter%2)
            ret += "%^BOLD%^";
        ret += "%^"+color+"%^";
        ret += convert_ascii(element);
        ret += "%^RESET%^";
        counter++;
    }
    return ret;
}

string bruise(string str) {
    string ret = "";
    int counter = 0;

    if( strlen(str) < 1 )
        return ret;

    foreach(mixed element in str) {
        if(counter%2)
            ret += "%^BLUE%^";
        else
            ret += "%^BOLD%^%^BLACK%^";
        ret += convert_ascii(element);
        ret += "%^RESET%^";
        counter++;
    }
    return ret;
}

string rainbow(string str) {
    string ret = "";
    string *colors = ({ 
            "RED", 
            "LIGHTRED", 
            "ORANGE", 
            "YELLOW", 
            "GREEN", 
            "LIGHTGREEN", 
            "GREY", 
            "WHITE", 
            "LIGHTCYAN", 
            "CYAN", 
            "LIGHTBLUE", 
            "BLUE", 
            "DARKGREY", 
            "MAGENTA", 
            "PINK"
            });
    string tmp = "";
    int len = 0;
    int seglen = 0;
    int pos = 0;
    int i = 0;
   
    if( strlen(str) < 1 )
        return ret;

    tmp = implode(filter(explode(str, ""), (: $1 != " " && $1 != "\t" && $1 != "\n" && $1 != "\r" ? 1 : 0 :)), "");
    len = strlen(tmp);

    if( len < 1 )
        return ret;

    seglen = min( ({ 5, max( ({ len / sizeof(colors), 1 }) ) }) );
    foreach( string letter in explode(str, "")) {
        if(letter == " " || letter == "\t" || letter == "\n" || letter == "\r") {
            ret += letter;
        } else {
            ret += "%^" + colors[i % sizeof(colors)] + "%^" + letter + "%^RESET%^";
            pos++;
        }
        if( pos >= seglen ) {
            i++;
            pos = 0;
        }
    }
    //write2("Result == \"" + ret + "\"\n");

    return ret;
}

string b64_encode(string s) {
    string b64chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    string array b;
    string r = "";
    string p = "";
    int i;
    int n;
    int n1, n2, n3, n4;
    int rlen, slen, plen;

    slen = sizeof(s);
    plen = slen % 3;
    b = rexplode(b64chars, "");

    for (i = 0; i < slen; i += 3) {
        n = s[i] << 16;

        if ((i+1) < slen)
            n += s[i+1] << 8;

        if ((i+2) < slen)
            n += s[i+2];

        n1 = (n >> 18) & 63;
        n2 = (n >> 12) & 63;
        n3 = (n >> 6) & 63;
        n4 = n & 63;

        r += "" + b[n1] + b[n2];

        if ((i+1) < slen)
            r += "" + b[n3];
       
        if ((i+2) < slen)
            r += "" + b[n4];
    }

    if (plen > 0)
        for (; plen < 3; plen++)
            r += "=";

    return r;
}

mixed b64_decode(string s) {
    string b64chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    string array b;
    string f = "";
    int i;
    int c;
    int n;
    int plen = 0;
    string r = "";

    b = rexplode( b64chars, "" );

    for (i = 0; i < sizeof(s); i++) {
        c = strsrch(b64chars, s[i]);
        if (c == -1) {
            // not found
            if (s[i] == 61) {
                // We found an "=", meaning we hit the padding.
                // For decoding purposes, "A" is a zero pad value here.
                f += "A";
                plen++;
                continue;
            } else if(s[i] == 32 || s[i] == 10 || s[i] == 9 || s[i] = 13) {
                // We found whitespace, skip it
                continue;
            } else {
                // invalid character
                return 0;
            }
        } else {
            f += b[c];
        }
    }

    if (sizeof(f) % 4)
        return 0;

    for (i = 0; i < sizeof(f); i += 4) {
        c = strsrch(b64chars, f[i]);
        n = c << 18;
        c = strsrch(b64chars, f[i+1]);
        n += c << 12;
        c = strsrch(b64chars, f[i+2]);
        n += c << 6;
        c = strsrch(b64chars, f[i+3]);
        n += c;

        r += "" + sprintf("%c%c%c", ((n >> 16) & 0xFF), ((n >> 8) & 0xFF), (n & 0xFF));
    }

    return r;
}

int cmdChannel(string verb, string str){
    string msg, name, rc, target, targetkey, target_msg, emote_cmd, remains;
    string *exploded;
    mixed array msg_data;
    object ob = 0;
    int i, emote, forcedemote;

    if(grepp(verb,"|")){
        string foo, bar;

        if(CHANNEL_PIPES){
            if(grepp(verb,"|bruise")){
                str = bruise(str);
                verb = replace_string(verb,"|bruise","");
            }
            if(grepp(verb,"|boldred")){
                str = boldify(str, "RED");
                verb = replace_string(verb,"|boldred","");
            }
            if(grepp(verb,"|boldyellow")){
                str = boldify(str, "YELLOW");
                verb = replace_string(verb,"|boldyellow","");
            }
            if(grepp(verb,"|boldgreen")){
                str = boldify(str, "GREEN");
                verb = replace_string(verb,"|boldgreen","");
            }
            if(grepp(verb,"|boldblue")){
                str = boldify(str, "BLUE");
                verb = replace_string(verb,"|boldblue","");
            }

            if(grepp(verb,"|rainbow")){
                str = rainbow(str);
                verb = replace_string(verb,"|rainbow","");
            }
            if(grepp(verb,"|gnomish")){
                str = gnomish(str);
                verb = replace_string(verb,"|gnomish","");
            }
            if(grepp(verb,"|novowel")){
                str = novowel(str);
                verb = replace_string(verb,"|novowel","");
            }

            if(grepp(verb,"|morse")){
                str = morse(str);
                verb = replace_string(verb,"|morse","");
            }

            if(grepp(verb,"|colorize")){
                str = dbz_colors(str);
                verb = replace_string(verb,"|colorize","");
            }

            if(grepp(verb,"|annoy")){
                str = dbz_colors(str,2);
                verb = replace_string(verb,"|annoy","");
            }
            if(grepp(verb,"|b64")){
                str = b64_encode(str);
                verb = replace_string(verb,"|b64","");
            }
            if(grepp(verb,"|d64")){
                str = b64_decode(str);
                verb = replace_string(verb,"|d64","");
            }
            if(grepp(verb,"|file")){
                if(!file_exists(str) || !(str = read_file(str))){
                    write("Can't read that file.");
                    return 0;
                }
                verb = replace_string(verb,"|file","");
            }
            if(grepp(verb,"|eval")){
                string file, filename, evaldefs;
                mixed ret;

                if(!member_group(previous_object(), PRIV_SECURE)) {
                    log_file("adm/eval", query_privs(previous_object())
                            +" ("+ctime(time())+"): eval "+str+"\n");
                }
                if( !str ) { notify_fail( "Syntax: \"chan|eval <lpc commands>\".\n" ); return 0; }
            
                evaldefs = "";
            
                filename = user_path(previous_object()->GetKeyName());
                if( !filename ) {
                    notify_fail( "Invalid previous_object GetKeyName?\n" );
                    return 0;
                }
                if( file_size( filename ) != -2 && !securep(previous_object()) ) {
                    notify_fail( "You must have a valid home directory!\n" );
                    return 0;
                }
            
                if(file_exists(filename + "evaldefs.h"))
                    evaldefs = "#include \"" + filename + "evaldefs.h\"\n";
            
                // The includes in the file aren't necessary (and can be removed if the
                // include files on your mud are called something different). They're
                // just to make things like "eval return children( USER )" possible.
                file =
                    ""+
                    "#include <lib.h>\n"+
                    "#include <cgi.h>\n"+
                    "#include <dirs.h>\n"+
                    "#include <privs.h>\n"+
                    "#include <logs.h>\n"+
                    "#include <virtual.h>\n"+
                    "#include <cfg.h>\n"+
                    "#include <clock.h>\n"+
                    "#include <save.h>\n"+
                    "#include ROOMS_H\n"+
                    "#include <localtime.h>\n"+
                    "#include <daemons.h>\n"+
                    "#include <network.h>\n"+
                    "#include <news.h>\n"+
                    "#include <objects.h>\n"+
                    "#include <commands.h>\n"+
                    "#include <armor_types.h>\n"+
                    "#include <damage_types.h>\n"+
                    "#include <position.h>\n"+
                    "#include <runtime_config.h>\n"+
                    "#include <terrain_types.h>\n"+
                    "#include <medium.h>\n"+
                    "#include <body_types.h>\n"+
                    "#include <size_types.h>\n"+
                    "#include <respiration_types.h>\n"+
                    "#include <message_class.h>\n"+
                    "inherit LIB_ITEM;\n"+
                    evaldefs+
                    "mixed eval() { " + str + "; }\n"+
                    "";
                filename += "CMD_EVAL_TMP_FILE.c";
                if(securep(previous_object())){
                    filename = "/secure/tmp/" + previous_object()->GetKeyName() +
                        "_CMD_EVAL_TMP_FILE.c";
                }
                rm( filename );
                if( ret = find_object( filename ) ) destruct( ret );
                write_file( filename, file,1 );
                ret = call_other(filename, "eval");
                //str = wrap( "Result = " + identify( ret ) );
                //str = identify(ret);
                str = ret;
                if( ret = find_object( filename ) ) destruct( ret );

                verb = replace_string(verb,"|eval","");
            }
        }

        if(sscanf(verb, "%s|%s", foo, bar) == 2) verb = foo;
    }

    if(grepp(verb, ":")){
        string *tmpv = explode(verb, ":");
        if(!sizeof(tmpv)) tmpv = ({"newbie"});
        verb = tmpv[0]+"emote";
        if(sizeof(tmpv) > 1) str = implode(tmpv[1..], ":") + str;
    }

    if(grepp(verb, ";")){
        string *tmpv = explode(verb, ";");
        if(!sizeof(tmpv)) tmpv = ({"newbie"});
        verb = tmpv[0]+"forcedemote";
        if(sizeof(tmpv) > 1) str = implode(tmpv[1..], ";") + str;
    }

    if(sizeof(str) > 2){
        if((str[0..0] == ":" || str[0..0] == ";") &&
                alphap(str[1..1]) && str[2..2] != " "){
            if(str[0..0] == ";" && !grepp(verb,"forcedemote")) 
                verb = replace_string(verb,"emote","") + "forcedemote";
            else if(str[0..0] == ":" && !grepp(verb,"emote")) verb += "emote";
            str = str[1..];
        }
    }

    //******LIST******
    //allow "list <chan>" to list users listening
    if( verb == "list" ) {
        string *who;
        string ch, mud;

        if( !str ) return 0;

        //Find the remote listing for a channel on a specific mud
        if( sscanf(str, "%s@%s", ch, mud) == 2 ) {
            mud = trim(mud);
            if(!alphap(last(mud,1))) mud = truncate(mud,1);

            if( !Channels[ch] ) return 0;

            if( member_array(this_player(), Channels[ch]) == -1 ) return 0;

            if( ch == (ch = GetRemoteChannel(ch)) ) {
                if(!creatorp(this_player())){
                    write("Remote channel information is not available to players.");
                    return 1;
                }
            }

            if( !(mud = INTERMUD_D->GetMudName(mud)) ) {
                this_player()->eventPrint(mud_name() + " is not aware of "+
                        "such a place.", MSG_ERROR);
                return 1;
            }

            if(!CanTalk(this_player(),verb)) {
                write("You lack privileges to that channel.");
                return 1;
            }
            SERVICES_D->eventSendChannelWhoRequest(ch, mud);
            this_player()->eventPrint("Remote listing request sent.",
                    MSG_SYSTEM);
            return 1;
        }
        else ch = str;

        //If no such channel, or not a part of that channel, then no list.
        if( !Channels[ch] ) return 0;
        if( member_array(this_player(), Channels[str]) == -1 ) return 0;

        //Build and print the list of listeners
        who = GetChannelList(str);
        msg = "Online: " + implode(who, "   ");
        this_player()->eventPrint(msg, MSG_SYSTEM);
        return 1;
    }
    //******END LIST******

    if( verb == "listall" ) {
        mapping i3_list;
        mapping imc2_list;
        mapping results = ([]);
        array i3k;
        array i2k;
        int n;

        i3_list = INTERMUD_D->GetMudList();
        i3k = keys(i3_list);
        imc2_list = IMC2_D->getmudinfo();
        i2k = keys(imc2_list);

        for( n = 0; n < sizeof(i3k); n++ ) {
            results[i3k[n]] = ({});
            // SERVICES_D->eventSendChannelWhoRequest(ch, i3k[i]);
        }
        for( n = 0; n < sizeof(i2k); n++ ) {
            results[i2k[n]] = ({});
            // IMC2_D->chanwho_out(
        }
        return 1;
    }

    //All emotes will fall into this IF structure and get tagged
    //as emote = 1 or forcedemote = 1

    //If it's a verb+emote, de-emote the verb, and mark as an emote
    if(grepp(verb, "emote")) {
        //Get the real channel
        if(grepp(verb, "forcedemote")){
            verb = replace_string(verb,"forcedemote","");   
            forcedemote = 1;
        }
        else verb = replace_string(verb,"emote","");
        emote = 1;
    }

    if(!strsrch(str,"^encode")) str = morse("(encoded):  "+str[7..]);

    if(find_object(INTERMUD_D) && !sizeof(remote_chans))
        remote_chans = distinct_array(INTERMUD_D->GetChannels());

    if(member_array(GetRemoteChannel(verb), remote_chans) == -1 &&
            member_array(verb, local_chans) == -1) local_chans += ({ verb });

    //******Access Checks
    //No talking if you're not allowed.
    if ( !CanTalk(this_player(),verb) ) {
        write("You lack privileges to that channel.");
        return 1;
    }
    //Toggle channel blocking
    if ( emote == 1 && ( !str || str == "" ) ) {
        this_player()->SetBlocked(verb + "emote");
        return 1;
    } else if ( !str || str == "" ) {
        this_player()->SetBlocked(verb);
        return 1;
    }
    //Syschans aren't for chatting on, only listening
    if ( member_array(verb, syschans) != -1 ) {
        write("This is not a channel for chatting.");
        return 1;
    }
    //If gagged, you can't talk on channels
    if ( this_player()->GetGagged(verb) ) {
        write("You have gag mode enabled. Type: 'gag off' to talk on channels.");
        return 1;
    }
    //Channel doesn't exist, probably an emote typo
    if(!Channels[verb]) return 0;
    //If not part of the channel, no chatting
    if( member_array(this_player(), Channels[verb]) == -1 ) return 0;
    //If blocked, allow no chatting
    if( this_player()->GetBlocked(verb) ) {
        if( this_player()->GetBlocked("all") ) {
            this_player()->eventPrint("You cannot chat while totally blocked.",
                    MSG_ERROR);
            return 1;
        }
        this_player()->eventPrint("Turn this channel on to talk on it.", MSG_ERROR);
        return 1;
    }
    //******End Access Checks

    //If there's no channel matching now, then it's a typo or wasn't meant for this file to pick up.
    if( !Channels[verb] ) return 0;
    //Find the remote channel's name, based on the local, pretty name
    rc = GetRemoteChannel(verb);
    //Check emotes
    if (emote == 1) {
        exploded = explode(str, " "); //BOOM!!! We have an array of words.
        if (sizeof(exploded) <= 1) {
            emote_cmd = str;
            remains = 0;
        } else {
            emote_cmd = exploded[0];
            remains = implode(exploded[1..]," ");
        }

        //***********************************
        //Find a target for targetted emotes.
        //***********************************
        if( !remains ) { //If no arguments and just one word
            msg_data = SOUL_D->GetChannelEmote(emote_cmd, ""); //Search for a feeling that matches
        } else { //If there is an argument to the emote,
            if( ob = find_living(target = convert_name(remains)) ) {  //If there is a living target
                msg_data = SOUL_D->GetChannelEmote(emote_cmd, "LIV");
                //If it's not there, get the emote's LVS text.
                //if (!msg_data)
                //	msg_data = SOUL_D->GetChannelEmote(emote_cmd, "LVS", remains);
            } else if( strsrch(target, "@") == -1 ) { //If no living target
                string array words = explode(remains, " ");
                target = "";
                for(i=0; i<sizeof(words); i++) {
                    target += lower_case(words[i]);
                    if( ob = find_living(target) ) {
                        if( i < sizeof(words)-1 ) {
                            remains = implode(words[(i+1)..], " ");
                        } else {
                            remains = 0;
                        }
                        //If it's not there, get the emote's LVS STR text.
                        if (!msg_data)
                            msg_data = SOUL_D->GetChannelEmote(emote_cmd, "LVS STR", remains);
                        break;
                    }
                }
                if( !ob ) {
                    msg_data = SOUL_D->GetChannelEmote(emote_cmd, "STR", remains);
                    target = 0;
                }

            } else {
                string array words;

                //Find any @'s in the remains.. Should be User@Mud
                i = strsrch(remains, "@", -1);

                //If there's not enough room for a proper name@mud, just do it as a string
                if ( i >= strlen(remains)-1 ) {
                    msg_data = SOUL_D->GetChannelEmote(emote_cmd, "STR", remains);
                    target = 0;
                } else { //Otherwise, call mud and find user
                    string mud,temp;
                    words = explode(remains[(i+1)..], " ");
                    target = remains[0..i];
                    remains = "";
                    while(sizeof(words)) {
                        temp = implode(words, " ");
                        temp = trim(temp);
                        if(!alphap(last(temp,1))) temp = truncate(temp,1);
                        mud = INTERMUD_D->GetMudName(lower_case(temp));
                        if (!mud) mud = IMC2_D->find_mud(lower_case(temp));
                        if( mud ) {
                            target += mud;
                            break;
                        }
                        if( remains == "" ) remains = words[<1];
                        else remains = words[<1] + " " + remains;
                        words = words[0..<2];
                    }

                    //If we couldn't find the mud,
                    if ( !mud ) {
                        msg_data = SOUL_D->GetChannelEmote(emote_cmd, "STR", remains);
                        target = 0;
                    } else {
                        if ( trim(remains) == "" ) {
                            msg_data = SOUL_D->GetChannelEmote(emote_cmd, "LIV");
                        } else {
                            msg_data = SOUL_D->GetChannelEmote(emote_cmd, "LIV STR", remains);
                        }
                    }
                }
            }
        } //Done finding target

        //***********************************
        //Find the target's proper name and target the message at him/her.
        //***********************************

        if ( msg_data ) { //There's a target
            string sgen = this_player()->GetGender();
            string tgen = 0;

            if ( ob ) { //If a local user
                target = ob->GetName();
                tgen = ob->GetGender();
            } else if ( target ) { //If a mud user
                string user, mud;

                sscanf(target, "%s@%s", user, mud);
                targetkey = target;
                tgen = SERVICES_D->GetRemoteGender(user, mud);
                target = SERVICES_D->GetRemoteDisplayName(user, mud);
                if( !target ) target = capitalize(targetkey);
            }

            //Calculate the viewpoint for 3rd parties
            str = create_message(POV_OBSERVER, msg_data[0][0],
                    msg_data[0][1], "$N", sgen, "$O", tgen,
                    msg_data[1]);

            //If it's targetted, calculate the message for the target
            if ( target ) {
                target_msg = create_message(POV_TARGET, msg_data[0][0],
                        msg_data[0][1], "$N", sgen,
                        "$O", tgen, msg_data[1]);
                target_msg = replace_string(target_msg, "$O's", "your");    
            }
        } else { //There's no target. Spurt it out like the user put it in.
            //Forced emotes only allow real emotes, not custom ones.
            if (forcedemote == 1) {
                if ( member_array( emote_cmd,SOUL_D->GetEmotes() ) > -1 ) {
                    write("Invalid syntax. See %^CYAN%^help "+emote_cmd+"%^RESET%^ for a list of usages.");
                    return 1;
                } else {
                    write("No such feeling. See %^CYAN%^help feelings%^RESET%^ for a list of feelings.");
                    return 1;
                }
            } else {
                str = "$N " + str;
                target = 0;
            }
        }
    }

    //If admin or cre channels, Capitalize a person's real name, because admins can be physically hidden
    if( verb == "admin" || verb == "cre" ) {
        if( !(name = this_player()->GetCapName()) )
            name = capitalize(this_player()->GetKeyName());
    }
    else name = this_player()->GetName();
    //Add the "Name" $N to the string
    if(!grepp(str,"$N") && emote) str = "$N "+str;
    //Send locally
    eventSendChannel(name, verb, str, emote, target, target_msg);
    //If it's a remote channel, send it remotely.
    if(member_array(GetRemoteChannel(verb), remote_chans) != -1
            && member_array(verb, local_chans) == -1){
        if (grepp(GetRemoteChannel(verb),":")) { //It's an IMC2 channel
            if(IMC2_D->getonline() != 1){
                return 1;
            }
            name = replace_string(name, " ", "");
            if( ob ) {
                IMC2_D->channel_out(name, rc, replace_string(replace_string(str,"$N ",""),"$O",target), emote);
            } else if ( targetkey ) {
                IMC2_D->channel_out(name, rc, replace_string(replace_string(str,"$N ",""),"$O",targetkey), emote);
            } else {
                IMC2_D->channel_out(name, rc, replace_string(str,"$N ",""), emote);
            }
        } else { //It's an I3 channel
            if( ob ) {
                SERVICES_D->eventSendChannel(name, rc, str, emote, convert_name(target), target_msg);
            } else {
                SERVICES_D->eventSendChannel(name, rc, str, emote, convert_name(targetkey), target_msg);;
            }
        }
    }
    return 1;
}

// Get an hour::minute timestamp
varargs string getDayTime(string this_time) {
    string the_time;

    if(undefinedp(this_time)) this_time = timestamp();
    the_time = this_time[11..15];
    the_time[2] = ':';

    return the_time;
}

// Get an hour:minute timestamp with daytime color.
varargs string getColorDayTime(string prefix, string suffix, string this_time) {
    string the_time;

    if(undefinedp(this_time)) this_time = timestamp();
    the_time = getDayTime(this_time);

    if(undefinedp(prefix)) prefix = "";
    if(undefinedp(suffix)) suffix = "";
    return hour_colors[the_time[0..1]] + prefix + the_time + suffix + "%^RESET%^";
}

// Sets a particular user to be a particular color.
int setSpeakerColor(string who, string color) {
    string shortwho;

    shortwho = lower_case(explode(who, "@")[0]);
    if (member_array(shortwho,keys(chatters)) >= 0) {
        chatters[shortwho] = color;
        SaveObject(SaveFile);
        return 1;
    }
    return 0;
}

// Figure out what color to make this guy.
string getSpeakerColor(string who) {
    string color, shortwho;

    shortwho = lower_case(explode(who, "@")[0]);
    if (member_array(shortwho,keys(chatters)) >= 0) {
        color = chatters[shortwho];
    } else {
        color = chat_colors[chat_counter % sizeof(chat_colors)];
        chatters[shortwho] = color;
        chat_counter++;
        SaveObject(SaveFile);
    }
    return color;
}

// Show who is mapped to a particular color.
mapping mapSpeakerColors() {
    int i;
    array k, v;
    mapping m;
    k = keys(chatters);
    v = values(chatters);
    m = ([]);

    for(i = 0; i < sizeof(k); i++) {
        m[v[i]] = undefinedp(m[v[i]]) ? ({ k[i] }) : m[v[i]] + ({ k[i] });
    }

    return m;
}

varargs string showSpeakerColors(string who) {
    int i;
    mapping m;
    string s = "";
    array k;
    int t = 0;
    int w;
    string color;

    if(!undefinedp(who)) {
        string shortwho = lower_case(explode(who, "@")[0]);
        if (member_array(shortwho,keys(chatters)) >= 0) {
            color = chatters[shortwho];
        }
    }
    w= this_player()->GetScreen()[0] || 80;
    m= mapSpeakerColors();
    k = sort_array(keys(m), 1);
    for(i = 0; i < sizeof(k); i++) {
        string nk;
        int c;
        string * lines;
        int j;

        if(!undefinedp(color)) {
            if(k[i] != color) {
                continue;
            }
        }
        nk = replace_string(k[i], "%^", "");
        c = sizeof(m[k[i]]);
        if(undefinedp(color)) {
            lines = explode(wrap(implode(sort_array(m[k[i]], 1), ", "), w - 30), "\n");
            for(j = 1; j < sizeof(lines); j++) {
                lines[j] = sprintf("%%^RESET%%^%29s%s%s", "", k[i], lines[j]);
            }
            s += sprintf("(%4d) %-20s: %s%s%s\n", c, nk, k[i], implode(lines, "\n"), "%^RESET%^");
        } else {
            s += sprintf("(%d) %s: %s%s%s\n", c, nk, k[i], implode(sort_array(m[k[i]], 1), ", "), "%^RESET%^");
        }

        t += c;
    }
    if(undefinedp(color)) {
        s += sprintf("(%4d) Total\n", t);
    }

    return s;
}

varargs string htmlSpeakerColors(string who) {
    int i;
    mapping m;
    string s = "";
    array k;
    int t = 0;
    int w;
    string color;

    if(!undefinedp(who)) {
        string shortwho = lower_case(explode(who, "@")[0]);
        if (member_array(shortwho,keys(chatters)) >= 0) {
            color = chatters[shortwho];
        }
    }
    w= this_player()->GetScreen()[0] || 80;
    m= mapSpeakerColors();
    k = sort_array(keys(m), 1);
    for(i = 0; i < sizeof(k); i++) {
        string nk;
        int c;
        string * lines;
        int j;

        if(!undefinedp(color)) {
            if(k[i] != color) {
                continue;
            }
        }
        nk = replace_string(k[i], "%^", "");
        c = sizeof(m[k[i]]);
        if(undefinedp(color)) {
            lines = explode(wrap(implode(sort_array(m[k[i]], 1), ", "), w - 30), "\n");
            for(j = 1; j < sizeof(lines); j++) {
                lines[j] = sprintf("%%^RESET%%^%29s%s%s", "", k[i], lines[j]);
            }
            s += sprintf("(%4d) %-20s: %s%s%s\n", c, nk, k[i], implode(lines, "\n"), "%^RESET%^");
        } else {
            s += sprintf("(%d) %s: %s%s%s\n", c, nk, k[i], implode(sort_array(m[k[i]], 1), ", "), "%^RESET%^");
        }

        t += c;
    }
    if(undefinedp(color)) {
        s += sprintf("(%4d) Total\n", t);
    }

    return s;
}

varargs string getColorSpeakerName(string speaker, string prefix, string suffix) {
    string speakercolor;

    if(undefinedp(prefix)) prefix = "";
    if(undefinedp(suffix)) suffix = "";
    speakercolor = getSpeakerColor(speaker);

    return speakercolor + prefix + speaker + suffix + "%^RESET%^";
}

string getChannelColor(string ch) {
    string chancolor;
    string prev = base_name(previous_object());

    if (member_array(lower_case(ch),keys(tags)) >= 0) { //If there's an entry for the channel
        chancolor = tags[lower_case(ch)]; //Use it
    } else { //Otherwise
        if(member_array(ch, local_chans) < 0 && (prev == IMC2_D ||
                        member_array(ch, (keys(INTERMUD_D->GetChannelList()) 
                                || ({}))) < 0)){
            chancolor = tags["default-IMC2"]; //Use the default IMC2 entry
        }
        else {
            chancolor = tags["default"]; //Use the default entry
        }
    }

    return chancolor;
}

varargs string getColorChannelName(string ch, string prefix, string suffix) {
    string chancolor;

    if(undefinedp(prefix)) prefix = "";
    if(undefinedp(suffix)) suffix = "";
    chancolor = getChannelColor(ch);

    return chancolor + prefix + ch + suffix + "%^RESET%^";
}

varargs string formChatString(string channel, string speaker, string msg, string this_time) {
    string the_time;
    string the_chan;
    string the_fool;
    string null;

    //string chatlayout = "%s %s<%s>%s %s";
    //string chatlayout = "%s says, %s(%s)%s '%s'";
    
    if(undefinedp(this_time)) this_time = timestamp();

    the_time = getColorDayTime(null, null, this_time);
    the_chan = getColorChannelName(channel, "<", ">");
    the_fool = getColorSpeakerName(speaker, "", ":");

    // HH:MM <ichat> foo@bar: blah blah blah
    return implode( ({ the_time, the_chan, the_fool, "" }), " ") + msg;
}

varargs string * formEmoteString(string channel, string speaker, string msg, string target, string target_msg, string this_time) {
    string the_time;
    string the_chan;
    string the_fool;
    string the_victim;
    string main_msg;
    string tar_msg;
    string msg_body;
    string null;

    if(undefinedp(this_time)) this_time = timestamp();

    //string emotelayout = "%s<%s>%s %s";
    //string emotelayout = "%s(%s)%s %s";
    
    //tn("CHAT_D->formEmoteString: "+identify(channel)+", "+identify(speaker)+", "+identify(msg)+
    //        ", "+identify(target)+", "+identify(target_msg), "green");
    main_msg = msg;
    the_time = getColorDayTime(null, null, this_time);
    the_chan = getColorChannelName(channel, "<", ">");
    the_fool = getColorSpeakerName(speaker);

    msg_body = replace_string(main_msg, "$N", speaker);
    main_msg = replace_string(main_msg, "$N", the_fool);
    if(!undefinedp(target) && !undefinedp(target_msg) && stringp(target) && stringp(target_msg)) {
        msg_body = replace_string(msg_body, "$O", target);
        main_msg = replace_string(main_msg, "$O", target);
        // Don't do this, as it can pollute the table with fake entries
        // or if you DO validate them, it'll be a lot of lag for a tiny
        // bit of bling...
        //the_victim = getColorSpeakerName(target);
        //main_msg = replace_string(main_msg, "$O", the_victim);
        
        tar_msg = target_msg;
        tar_msg = replace_string(tar_msg, "$N", the_fool);
        tar_msg = replace_string(tar_msg, "$O", "you");
    }

    // HH:MM <ichat> foo@bar blah blah blah
    main_msg = implode( ({ the_time, the_chan, "" }), " ") + main_msg;
    return ({ main_msg, tar_msg, msg_body });
}

int shouldIgnore(object listener, string who) {
    int ignore = 0;
    string suspect, site;

    if(sscanf(who, "%s@%s", suspect, site) < 2) {
        suspect = who;
        site = "@"+mud_name();
    }
    else site = "@"+site;
    if(sizeof(listener->GetMuffed())) {
        foreach(string jerk in listener->GetMuffed()) {
            if(jerk && lower_case(suspect) == lower_case(jerk)) ignore = 1;
            if(jerk && lower_case(site) == lower_case(jerk)) ignore = 1;
            if(jerk && (lower_case(suspect) + lower_case(site)) == lower_case(jerk)) ignore = 1;
        }
    }

    return ignore;
}

varargs void eventChannelMsgToListeners(string who, string ch, string msg,
        int emote, string target, string targmsg) {
    object *obs;
    object ob;
    string tmp;

    if( target && (ob = find_player(convert_name(target))) ) {
        target = ob->GetName();
    }

    if(Channels[ch]){
        obs = filter(Channels[ch], (: $1 && !($1->GetBlocked($(ch))) :));
        foreach(object listener in obs) {
            if( emote == 1 && listener == ob) continue;
            if( shouldIgnore(listener, who) ) continue;
            tmp = msg;
            if(listener->GetNoChanColors()) tmp = decolor(tmp);
            if(CanListen(listener,ch) && !(listener->GetMuted(ch)))
                listener->eventPrint(tmp, MSG_CHAN);
        }
        if( emote == 1 && ob && member_array(ob, obs) != -1 ) {
            if( !(ob->GetBlocked(ch)) ) {
                if( !shouldIgnore(ob, who) ) {
                    tmp = targmsg;
                    if(ob->GetNoChanColors()) tmp = decolor(tmp);
                    if(CanListen(ob,ch)&& !(ob->GetMuted(ch)))
                        ob->eventPrint(tmp, MSG_CHAN);
                }
            }
        }
    }
}

varargs void eventSendChannel(string who, string ch, string msg, int emote,
        string target, string targmsg) {
    object channeler = find_player(lower_case(who));
    int terminal;
    string prev = base_name(previous_object());
    string pchan,pmsg;
    string *whobits;
    string *msgbits;
    string *cmsgbits;
    string cmsg;
    string bit;
    string tstamp;
    string output;
    int i;

    //tn("CHAT_D->eventSendChannel: "+identify(who)+", "+identify(ch)+", "+identify(msg), "green");

    if(prev == INSTANCES_D){
        terminal = 1;
    }
    if(prev == SERVICES_D) terminal = 1;
    if(prev == IMC2_D) terminal = 1;
    if(!terminal){
        string rch = GetRemoteChannel(ch);
        if(member_array(rch, remote_chans) == -1){
            //tn("CHAT_D->eventSendChannel: handing off to INSTANCES_D.", "green");
            INSTANCES_D->eventSendChannel(who,ch,msg,emote,target,targmsg);
        }
    }

    pchan=ch;
    if(!channeler) channeler = this_player();
    if(!strsrch(msg,"-.--. . -. -.-. --- -.. . -.. -.--.- ---...")) msg = unmorse(msg);
    if(targmsg && !strsrch(targmsg,"-.--. . -. -.-. --- -.. . -.. -.--.- ---..."))
        targmsg = unmorse(targmsg);

    if(this_player() && this_player() != channeler) channeler = this_player();

    if(!strsrch(base_name(previous_object()), "/realms/") ||
            !strsrch(base_name(previous_object()), "/open/")) {
        return 0;
    }

    if(member_array(ch, syschans) != -1) {
        //tn("CHAT_D->eventSendChannel: emote = 0 due to syschans.", "green");
        emote = 0;
    }
    if(channeler){
        if(!CanTalk(channeler, ch) && member_array(ch, syschans) == -1){
            //tn("CHAT_D->eventSendChannel: !CanTalk(ch)", "green");
            return;
        }
    }
    if( file_name(previous_object()) == SERVICES_D || 
            file_name(previous_object()) == IMC2_D) {
        ch = GetLocalChannel(ch);
        if( emote && sizeof(who)) {
            //msg = replace_string(msg, "$N", who);
            //tn("CHAT_D->eventSendChannel: emote + who + SERVICES: "+identify(ch), "green");
            //DEBUG
            //msg = replace_string(msg, "$N", getColorSpeakerName(who, "<SERV_IMC2:", ":SERV_IMC2>"));
            msg = replace_string(msg, "$N", getColorSpeakerName(who));
            pmsg = replace_string(msg, "$N", who);
        }
    }
    else
    {
        if( origin() != ORIGIN_LOCAL &&
            previous_object() != master() &&
            file_name(previous_object()) != PARTY_D && 
            file_name(previous_object()) != UPDATE_D && 
            file_name(previous_object()) != INSTANCES_D && 
            member_array(ch, syschans) == -1)
        {
            //tn("CHAT_D->eventSendChannel: ORIGIN_LOCAL/master/etc check.", "green");
            return;
        }
    }
    //tn("CHAT_D->eventSendChannel: after SERVICES check.", "green");
    prev = file_name(previous_object());
    if(!Channels[ch] && prev != SERVICES_D && prev != INSTANCES_D){
        //tn("CHAT_D->eventSendChannel: Channels[ch] check.", "green");
        return;
    }
    if( emote ) {
        object ob;
        string *results;

        //tn("CHAT_D->eventSendChannel: in emote.", "green");
        if( target && (ob = find_player(convert_name(target))) ) {
            //tn("CHAT_D->eventSendChannel: target && find_player.", "green");
            target = ob->GetName();
        }

        //tn("CHAT_D->eventSendChannel: just before formEmoteString.", "green");
        results = formEmoteString(ch, who, msg, target, targmsg);
        //tn("CHAT_D->eventSendChannel: just after formEmoteString.", "green");
        msg = results[0];
        targmsg = results[1];
        pmsg = results[2];

        //tn("CHAT_D->eventSendChannel: emote code: "+identify(results)+", "+identify(who)+", "+identify(ch)
        //        +", "+identify(msg)+", "+identify(emote)+", "+identify(target)+", "
        //        +identify(targmsg), "green");

        tn("CHAT_D->eventSendChannel: in emote, just before strip_colours(pmsg); \"" + pmsg + "\"", "green");
        pmsg = strip_colours(pmsg);
        tn("CHAT_D->eventSendChannel: in emote, just after strip_colours(pmsg); \"" + pmsg + "\"", "green");
        //pmsg = TERMINAL_D->no_colours(pmsg);
        eventAddLast(ch, msg, pchan, pmsg);
        eventChannelMsgToListeners(who, ch, msg, emote, target, targmsg);
    }
    else
    {
        pmsg = msg;
        cmsg = msg;

        msg = formChatString(ch, who, msg);
        //tn("CHAT_D->eventSendChannel: not emote?", "green");
        pmsg = strip_colours(pmsg);
        //pmsg = TERMINAL_D->no_colours(pmsg);
        eventAddLast(ch, msg, pchan, pmsg, who);
        eventChannelMsgToListeners(who, ch, msg, emote, target, targmsg);
    }
    whobits = explode(who, "@");
    if(sizeof(whobits) < 2) {
        if(sizeof(whobits) < 1) {
            whobits += ({ "Someone" });
        }
        whobits += ({ mud_name() });
    }
    who = implode(whobits, "@");
    msgbits = rexplode(pmsg, "\n");
    cmsgbits = rexplode(cmsg, "\n");
    tstamp = timestamp();

    i = 0;
    foreach(bit in msgbits) {
        output = sprintf("%s%03d\t%s\t%s\t%s\n", tstamp, i, ch, who, bit);
        i++;
        LogIt(output, "/secure/log/allchan.log", ch);
    }
    i = 0;
    foreach(bit in cmsgbits) {
        output = sprintf("%s%03d\t%s\t%s\t%s\n", tstamp, i, ch, who, bit);
        i++;
        LogIt(output, "/secure/log/allchan_color.log", ch);
    }
    
    //LogIt(timestamp()+"\t"+ch+"\t"+who+"\t"+pmsg+"\n", "/secure/log/allchan.log", ch);
    //tn("CHAT_D->eventSendChannel: return?", "green");
}

string *GetChannelList(string ch) {
    string *ret;
    object who;

    if( file_name(previous_object()) == SERVICES_D ) ch = GetLocalChannel(ch);
    else if( origin() != ORIGIN_LOCAL ) return ({});
    if( !Channels[ch] ) return ({});
    ret = ({});
    foreach(who in Channels[ch]) {
        if( !who || who->GetInvis() || who->GetBlocked(ch) )
            continue;
        ret += ({ who->GetName() });
    }
    return ret;
}

string *GetLocalChannels(){
    return copy(local_chans);
}

string GetLocalChannel(string ch) {
    if(ch && !strsrch(ch,"server0")){
        ch = replace_string(ch, "server01", "Server01");
        ch = replace_string(ch, "server02", "Server02");
    }
    if (sizeof(localchans[ch])) return localchans[ch];
    else return ch;
}

string GetRemoteChannel(string ch) {
    if (sizeof(remotechans[ch])) return remotechans[ch];
    else return ch;
}

int GetListening(object player, string ch){
    if(!Channels[ch] || 
            member_array(player, Channels[ch]) == -1) return 0;
    return 1;
}

string *GetChannels() { return sort_array(copy(keys(Channels)),1); }
string *GetSystemChannels() { return sort_array(copy(syschans),1); }
mapping GetTags() { return copy(tags); }
string GetTag(string ch) { return tags[ch]; }
