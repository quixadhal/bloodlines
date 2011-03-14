/* i3irc_d.c

 Tricky @ Rock the Halo
 21-JUN-06
 I3 <-> IRC Bridge Daemon

*/

/* NOTICE: THIS IS BETA SOFTWARE - USE AT OWN RISK! <-- WARNING */

/* Preproccessor statements */

#include <socket.h>

#define MUD 0
#define STREAM 1
#define DATAGRAM 2
#define STREAM_BINARY 3
#define DATAGRAM_BINARY 4

#define SOCKET_STREAM 1
#define SOCKET_STREAM_LISTEN 2
#define SOCKET_DATAGRAM 3
#define SOCKET_MUD 4
#define SOCKET_MUD_LISTEN 5
#define SOCKET_ACQUIRE 6

#include <logs.h>
#include <mudlib.h>
#include <global.h>

#define SOCKET_D "/adm/daemons/socket_d"

#define LOG(f,m) log_file(f,sprintf("%s:%s", ctime(time())[4..18], m))
#define DATA_FILE "/data/daemons/i3irc"
#define MUDNAME mud_name()
#define HEART_BEATS 60

#define HISTORY_SIZE 500
#define HISTORY_FILE "/data/daemons/i3irc"

#if 1
#define I3_ROUTER_NAME   "*yatmim"
#define I3_ROUTER_IP     "149.152.218.102"
#define I3_ROUTER_PORT   23
#else
#define I3_ROUTER_NAME   "*i4"
#define I3_ROUTER_IP     "204.209.44.3"
#define I3_ROUTER_PORT   8080
#endif

#define IRC_SERVER_IP    "213.219.249.66"
#define IRC_SERVER_PORT  8001

/* Private Variables */
static mapping history;
static mapping mudlist_buffer;

static string *ircWhoList;
static string *chantypes;
static int *reserved;

static int s_irc, s_i3;
static int i3_isBlocked = 0;
static int irc_isBlocked = 0;
static mixed *i3_msg_buffer = ({ });
static mixed *irc_msg_buffer = ({ });
static string dataline;
static int i3_router = 0;
static string irc_addr_ip = "";

static string datafile;

static int idle_timer = 0;
static int hb_timer = 0;

static int reconnect_wait;

/* Public variables */
string admin_password = ".........................................";
mapping registered;
string *identified;
mapping data;

/* Prototypes */
void setHistory(string key, string *val);
void addHistory(string key, string *val);
void remHistory(string key);
mapping getHistory();
void i3_create();
void irc_create();
void i3_remove();
void irc_remove();
void setup_services();
int i3_setup_socket(string host, int port);
int irc_setup_socket(string host, int port);
void i3_connect();
void irc_connect();
void i3_read_callback(int fd, mixed packet);
void i3_close_callback(int fd);
void irc_read_callback(int fd, mixed msg);
void irc_close_callback(int fd);

varargs void send_packet(mixed *packet, int quiet);
varargs void send_packets(mixed *packets, int quiet);
varargs int irc_write(string msg, int quiet);

void heart_beat();

void i3_send_channel_listen(string channel, int flag);
int i3_channel_filter(string channel);

void bot_reply(string from, string cmd);
int bot_cmd(string from, string cmd);

/* Functions */
void add_i3_map_chan(string i3_chan, string *irc_chans)
{

    if(member_array(i3_chan, data["i3_chans"]) == -1)
    {
        data["i3_chans"] += ({ i3_chan });
        data["i3_map_chans"] = ([ i3_chan: irc_chans ]);
        i3_send_channel_listen(i3_chan, 1);
    }
    else
    {
        string *tmp = data["i3_map_chans"][i3_chan] - irc_chans;

        data["i3_map_chans"][i3_chan] += tmp;
    }

}

void add_irc_map_chan(string irc_chan, string *i3_chans)
{

    if(member_array(irc_chan, data["irc_chans"]) == -1)
    {
        string chan;

        data["irc_chans"] += ({ irc_chan });
        data["irc_map_chans"] = ([ irc_chan: i3_chans ]);

        CHAN_D->registerCh("IRC", irc_chan);

        irc_write("JOIN " + irc_chan);

        chan = replace_string(irc_chan, "#", "~");

        if(file_exists(HISTORY_FILE + "." + chan + ".o"))
            setHistory(irc_chan, restore_variable(read_file(HISTORY_FILE + "." + chan + ".o")));
        else
            setHistory(irc_chan, ({ }) );

   }
    else
    {
        string *tmp = data["irc_map_chans"][irc_chan] - i3_chans;

        data["irc_map_chans"][irc_chan] += tmp;
    }

}

mapping getMudList() { return copy(data["i3_mudlist"]); }
string *getMuds() { return keys(data["i3_mudlist"]); }
string *getLCMuds()
{
    string *orig_arr, *new_arr;
    orig_arr = getMuds();
    new_arr = ({ });
    foreach(string name in orig_arr)
        new_arr += ({ lower_case(name) });
    return new_arr;
}

mapping getChannelList() { return copy(data["i3_chanlist"]); }
string *getChannels() { return keys(data["i3_chanlist"]); }

string *getListening() { return copy(data["i3_chans"]); }

string trim(string str)
{
    int i, sz;
    int pos_left, pos_right;

    if (str == "") return str;

    i = 0; sz = strlen(str);
    while (i < sz && str[i] == ' ') i++;
    pos_left = i;
    i = sz - 1;
    while (i > pos_left && (str[i] == ' ' || str[i] == '\r' || str[i] == '\n')) i--;
    pos_right = i;

    return str[pos_left..pos_right];
}

void create()
{
    LOG(LOG_I3IRC, "Reloading with previous_object being: " + file_name(previous_object()) + "\n");

    idle_timer = 0;
        hb_timer = 0;

    registered = ([
      "tricky247": ".........................................",
      "tricky248": ".........................................",
      "tricky@rock the halo": ".........................................",
    ]);
    identified = ({
      "tricky247",
      "tricky248",
      "tricky@rock the halo",
    });

    data = ([ ]);

    /* IRC Sever replies that are:
     *   1. No longer in use.
     *   2. Reserved for future planned use.
     *   3. In current use but are part of a non-generic 'feature'
     *      of the current IRC server.
     */
    reserved = ({
      209, 217, 231, 232, 233, 234, 235,
      316, 361, 362, 363, 373, 384,
      466, 476, 492
    });

    ircWhoList = ({ });

    reconnect_wait = 10;

    i3_create();
    irc_create();

    datafile = DATA_FILE + "_" + I3_ROUTER_NAME[1..<1];

    if(file_exists(datafile + ".o")) restore_object(datafile);

    data["i3_router_list"] = ({
      ({ I3_ROUTER_NAME, I3_ROUTER_IP + " " + I3_ROUTER_PORT }),
    });

    i3_router++;

    if(i3_router > sizeof(data["i3_router_list"])) i3_router = 1;

    data["i3_router"] = ({ data["i3_router_list"][i3_router - 1] });
    data["irc_server"] = IRC_SERVER_IP + " " + IRC_SERVER_PORT;
    history = ([ ]);

    data["realname"] = "LPUniversity mudlib - " + data["i3_router"][0][0] + " I3 <-> IRC Bridge Daemon";
    data["listening"] = ({ });

    if(i3_setup_socket(I3_ROUTER_IP, I3_ROUTER_PORT) < 0)
    {
        LOG(LOG_I3IRC, "Error [I3]: Unable to setup socket.\n");
        destruct();

        return;
    }

    if(irc_setup_socket(IRC_SERVER_IP, IRC_SERVER_PORT) < 0)
    {
        LOG(LOG_I3IRC, "Error [IRC]: Unable to setup socket.\n");
        destruct();

        return;
    }

    setup_services();
    i3_connect();
    irc_connect();

    set_heart_beat(1);

    write("Success [I3IRC]: "+data["realname"]+" Active (" + query_privs(this_object()) + ").\n");

    save_object(datafile);
}

void heart_beat()
{
    int changed = 0;

    /* Heart beats are 2 seconds long on LPUni by standard */
    idle_timer += 2;
    hb_timer += 2;

    if(hb_timer >= 24 * 60 * 60) hb_timer = 0;

    if(hb_timer % 15 == 0 && sizeof(keys(mudlist_buffer)) > 0)
    {

        foreach(string key, mixed val in mudlist_buffer)
        {

            if(key && val)
            {
                LOG(LOG_I3IRC, "Notice [I3]: Procesing mud: " + identify(key) + "\n");
                LOG(LOG_I3IRC, "Notice [I3]: Procesing mudinfo: " + identify(val) + "\n");
            }

            if(!val && data["i3_mudlist"][key] != 0)
                map_delete(data["i3_mudlist"], key);
            else
            if(val)
                data["i3_mudlist"][key] = val;

            map_delete(mudlist_buffer, key);
            changed = 1;
        }

    }

    if(hb_timer % HEART_BEATS == 0)
    {

        foreach(mixed key, mixed val in data["i3_mudlist"])
        {

            if(!val || val[0] == 0)
            {
                LOG(LOG_I3IRC, "Notice [heart_beat]: Deleting mud '"+key+"' from the mudlist.\n");
                map_delete(data["i3_mudlist"], key);

                changed = 1;
            }

        }

        foreach(mixed key, mixed val in data["i3_session_keys_by_name"])
        {
            int diff = time() - val["registered"];

            if(diff > 10 * 60)
            {
                LOG(LOG_I3IRC, "Notice [heart_beat]: Un-registering session key for "+key+"\n");
                map_delete(data["i3_session_keys_by_session"], val["session_key"]);
                map_delete(data["i3_session_keys_by_name"], key);

                changed = 1;
            }

        }

    }

    if(changed) save_object(datafile);
}

void setHistory(string key, string *val)
{
    history[key] = val;

    if(sizeof(history[key]) > HISTORY_SIZE)
    {
        string chan;

        history[key] = history[key][sizeof(history[key]) - HISTORY_SIZE..sizeof(history[key]) - 1];
        chan = replace_string(key, "#", "~");
        write_file(HISTORY_FILE + "." + chan + ".o", save_variable(history[key]), 1);
    }

}

void addHistory(string key, string *val)
{
    string *msg = ({ }), chan;

    if(!history[key]) history[key] = ({ });

    if(sizeof(history[key]) > HISTORY_SIZE) history[key] = history[key][1..sizeof(history[key]) - 1];

    foreach(string line in val) msg += ({ sprintf("%s %s", ctime(time())[4..18], line) });

    history[key] += msg;
    chan = replace_string(key, "#", "~");
    write_file(HISTORY_FILE + "." + chan + ".o", save_variable(history[key]), 1);
}

void remHistory(string key) { map_delete(history, key); }

mapping getHistory() { return copy(history); }

void i3_create()
{
    // InterMud3 data.
    data["i3_nick"] = "IRC_Bridge";
    data["i3_chans"] = ({ "lpuni", "imud_code", });
    data["i3_map_chans"] = ([ "lpuni": ({ "#lpuni" }), "imud_code": ({ "##lpc" }) ]);

    data["i3_router"] = ({ });
    data["i3_router_list"] = ({ ({ I3_ROUTER_NAME, I3_ROUTER_IP + " " + I3_ROUTER_PORT }) });
    data["i3_router_password"] = 0;
    data["i3_mudlist_id"] = 0;
    data["i3_mudlist"] = ([ ]);
    data["i3_chanlist_id"] = 0;
    data["i3_chanlist"] = ([ ]);
    data["i3_services"] = ([ ]);
    data["i3_other"] = ([ ]);

    data["i3_session_keys_by_session"] = ([ ]);
    data["i3_session_keys_by_name"] = ([ ]);

    s_i3 = -1;
    i3_isBlocked = 0;
    i3_msg_buffer = ({ });

    mudlist_buffer = ([ ]);
}

void i3_reconnect()
{
    reconnect_wait += 10;

    if(reconnect_wait > 300) reconnect_wait = 300;

    i3_create();

    if(file_exists(datafile + ".o")) restore_object(datafile);

    i3_router++;

    if(i3_router > sizeof(data["i3_router_list"])) i3_router = 1;

    data["i3_router"] = ({ data["i3_router_list"][i3_router - 1] });
    data["realname"] = "LPUniversity mudlib - " + data["i3_router"][0][0] + " I3 <-> IRC Bridge Daemon";

    data["listening"] = ({ });

    if(i3_setup_socket(I3_ROUTER_IP, I3_ROUTER_PORT) < 0)
    {
        LOG(LOG_I3IRC, "Error [I3]: Unable to setup socket.\n");
        destruct();

        return;
    }

    setup_services();
    i3_connect();

    save_object(datafile);
}

void irc_create()
{
    // IRC data.
    data["irc_nick"] = "i3_bridge";
    data["irc_chans"] = ({ "#lpuni", "##lpc" });
    data["irc_map_chans"] = ([ "#lpuni": ({ "lpuni" }), "##lpc": ({ "imud_code" }) ]);
    data["irc_server"] = IRC_SERVER_IP + " " + IRC_SERVER_PORT;

    CHAN_D->registerModule("IRC", file_name(this_object()));

    resolve("chat.eu.freenode.net", "rsv_cb");
}

void rsv_cb(string a, string r, int k)
{
    LOG(LOG_I3IRC, "Notice [IRC]: Resolved '"+a+"' into '"+r+"'\n");
    irc_addr_ip = r;
}

void irc_reconnect()
{
    irc_create();

    if(irc_setup_socket(IRC_SERVER_IP, IRC_SERVER_PORT) < 0)
    {
        LOG(LOG_I3IRC, "Error [IRC]: Unable to setup socket.\n");
        destruct();

        return;
    }

    irc_connect();
}

void remove()
{
    LOG(LOG_I3IRC, "Notice [I3IRC]: "+data["realname"]+" Destructing.\n");

    i3_remove();
    irc_remove();

    destruct();
}

void i3_remove()
{
    save_object(datafile);

    foreach(mixed *cout in call_out_info())
    {

        if(cout[0] == this_object())
            if(cout[1] == "i3_reconnect") remove_call_out("i3_reconnect");

    }

    send_packet( ({
      "shutdown",
      5,
      mud_name(),
      0,
      data["i3_router"][0][0],
      0,
      1
    }) );

    SOCKET_D->socketClose("I3");

    LOG(LOG_I3IRC, "Notice [I3]: I3 connection closed.\n");
}

void irc_remove()
{
    save_object(datafile);

    foreach(mixed *cout in call_out_info())
    {

        if(cout[0] == this_object())
            if(cout[1] == "irc_reconnect") remove_call_out("irc_reconnect");

    }

    irc_write("QUIT :" + data["realname"]);

    /* The socket should already be closed by the IRC server */
    //SOCKET_D->socketClose("IRC");

    CHAN_D->unregisterModule("IRC");

    LOG(LOG_I3IRC, "Notice [IRC]: IRC connection closed.\n");
}

void setup_services()
{
    /* SERVICES SETUP HERE */
    /* Ensure they get added to (mapping) data["i3_services"] */

    data["i3_services"] = ([
      "tell": 1,
      "who": 1,
      "locate": 1,
      "channel": 1,
      "auth": 1,
    ]);
}

void i3_connect()
{
    LOG(LOG_I3IRC, "Notice [I3]: Sending connection details.\n");

    send_packet( ({
      "startup-req-3",
      5,
      mud_name(),
      0,
      data["i3_router"][0][0],
      0,
      data["i3_router_password"],
      data["i3_mudlist_id"],
      data["i3_chanlist_id"],

      /* These correspond to the values in a mudlist info_mapping */
      port(),
      0,
      0,
      lib_name() + " " + lib_version(),
      baselib_name() + " " + baselib_version(),
      driver_version(),
      "LPMud",
      open_status(),
      admin_email(),
      data["i3_services"],
      data["i3_other"]
    }) );
}

void irc_connect()
{
    LOG(LOG_I3IRC, "Notice [IRC]: Sending connection details.\n");

    irc_isBlocked = 0;
    irc_msg_buffer = ({ });
    dataline = "";

    irc_write("NICK "+data["irc_nick"]);
    irc_write("USER "+data["irc_nick"]+" . . :"+data["realname"]);

    call_out("irc_delayed_connect1", 5);
    call_out("irc_delayed_connect2", 45);
}

void irc_delayed_connect1()
{
    irc_write(":"+data["irc_nick"]+" MODE "+data["irc_nick"]+" +iw");
    irc_write("PRIVMSG NickServ :IDENTIFY ...........", 1);
    irc_write("PRIVMSG NickServ :SET AUTOMASK ON");
}

void irc_delayed_connect2()
{

    foreach(string val in data["irc_chans"])
    {
        string chan;

        irc_write("JOIN "+val);
        CHAN_D->registerCh("IRC", val);

        chan = replace_string(val, "#", "~");

        if(file_exists(HISTORY_FILE + "." + chan + ".o"))
            setHistory(val, restore_variable(read_file(HISTORY_FILE + "." + chan + ".o")));
        else
            setHistory(val, ({ }) );

    }

}

int i3_setup_socket(string host, int port)
{
    SOCKET_D->setSocketReadCB("i3_read_callback");
    SOCKET_D->setSocketCloseCB("i3_close_callback");
    SOCKET_D->setSocketType(MUD);
    SOCKET_D->setSocketLogFile(LOG_I3IRC);

    return SOCKET_D->socketClientCreate("I3", host, port);
}

int irc_setup_socket(string host, int port)
{
    SOCKET_D->setSocketReadCB("irc_read_callback");
    SOCKET_D->setSocketCloseCB("irc_close_callback");
    SOCKET_D->setSocketType(STREAM);
    SOCKET_D->setSocketLogFile(LOG_I3IRC);

    return SOCKET_D->socketClientCreate("IRC", host, port);
}

void i3_close_callback(int fd)
{
    save_object(datafile);

    idle_timer = 0;

    LOG(LOG_I3IRC, "Warning [I3/socket]: Close callback called - Connection Terminated.\n");
    LOG(LOG_I3IRC, "Notice [I3/socket]: Re-establishing connection in " + reconnect_wait + " seconds.\n");

    foreach(mixed *cout in call_out_info())
    {

        if(cout[0] == this_object())
            if(cout[1] == "i3_reconnect") remove_call_out("i3_reconnect");

    }

    call_out("i3_reconnect", reconnect_wait);

    LOG(LOG_I3IRC, "Success [I3/socket]: I3 connection closed.\n");
}

void irc_close_callback(int fd)
{
    save_object(datafile);

    idle_timer = 0;

    LOG(LOG_I3IRC, "Warning [IRC/socket]: Close callback called - Connection Terminated.\n");
    LOG(LOG_I3IRC, "Notice [IRC/socket]: Re-establishing connection in 10 seconds.\n");

    foreach(mixed *cout in call_out_info())
    {

        if(cout[0] == this_object())
            if(cout[1] == "irc_reconnect") remove_call_out("irc_reconnect");

    }

    call_out("irc_reconnect", 10);

    LOG(LOG_I3IRC, "Success [IRC/socket]: IRC connection closed.\n");
}

varargs void send_packet(mixed *packet, int quiet)
{

    if(packet[0] != "who-reply") idle_timer = 0;

    if(stringp(packet[3]))
        packet[3] = lower_case(packet[3]);

    if(stringp(packet[5]))
        packet[5] = lower_case(packet[5]);

    if(!quiet) LOG(LOG_I3IRC, "Notice [I3]: Sending " + identify(packet) + "\n");

    SOCKET_D->socketClientWrite("I3", packet);
}

varargs void send_packets(mixed *packets, int quiet)
{
    foreach(mixed *packet in packets) send_packet(packet, quiet);
}

varargs void irc_write(string msg, int quiet)
{
    string *ansi_words = ({
      "RESET", "BOLD", "FLASH",
      "RED", "GREEN", "YELLOW",
      "BLUE", "CYAN", "MAGENTA",
      "BLACK", "WHITE",
      "B_RED", "B_GREEN", "B_ORANGE", "B_YELLOW",
      "B_BLUE", "B_CYAN", "B_MAGENTA",
      "B_BLACK", "B_WHITE",
      "STATUS", "WINDOW", "INITTERM",
      "ENDTERM", "FRTOP", "FRBOTTOM",
      "UNFR", "SAVEC", "RESTC",
      "HOMEC", "CLEAR", "ER_SOL",
      "ER_EOL", "ER_LINE", "ER_DOWN",
      "ER_UP"
    });
    string *words;
    int size;

    if(msg == "") return;

    if(msg[0..3] != "PONG") idle_timer = 0;

    size = sizeof(words = explode(msg + "%^", "%^"));

    while(size--)
        if(member_array(words[size], ansi_words) != -1)
            words[size] = "";

    msg = implode(words, "");

    if(!quiet) LOG(LOG_I3IRC, "Notice [IRC]: Sending " + msg + "\n");

    SOCKET_D->socketClientWrite("IRC", msg + "\r");
}

void i3_read_callback(int fd, mixed packet)
{
    string func, err;

    if(!sizeof(packet)) return;

    if(!arrayp(packet)) err = "packet not array";
    else
    if(sizeof(packet) <= 5) err = "packet size too small";
    else
    if(stringp(packet[4]) && packet[4] != mud_name())
        err = "wrong destination mud";
    else
    if(!stringp(packet[2])) err = "originating mud not a string";
    else
    if(!stringp(packet[0])) err = "SERVICE is not a string";

    if(err)
    {
        LOG(LOG_I3IRC, "Error [I3]: " + err + ".\n" + sprintf("%O", packet) + "\n");

        return;
    }

    if(packet[0] != "mudlist")
        LOG(LOG_I3IRC, "Notice [I3]: Received " + identify(packet) + "\n");
    else
        LOG(LOG_I3IRC, "Notice [I3]: Received " + packet[0] + "\n");

    /* Sanity check on the originator username */
    if(stringp(packet[3]))
        packet[3] = lower_case(packet[3]);

    /* Sanity check on the target username */
    if(stringp(packet[5]))
        packet[5] = lower_case(packet[5]);

    func = "i3_rec_" + packet[0];
    func = replace_string(func, "-", "_");

    if(function_exists(func, this_object()))
        call_other(this_object(), func, packet);
    else
        call_other(this_object(), "i3_rec_unsupported", packet);

    if(packet[0] != "who-req") idle_timer = 0;

}

void irc_service_handler(string str)
{
    string func;
    string str1, str2, str3; // parts of the text
    string blah1, blah2;
    int servtype;

    // LOG(LOG_I3IRC, "Notice [IRC]: Parsing '"+str+"'\n");

    sscanf(str, "%s %s", str1, str2); // split first word from rest

    // Handle special cases.
    switch (str1)
    {
        case "NOTICE":
        case "ERROR":
          return;
        case "PING":
          // respond to pings...
          sscanf(str2, ":%s", str2);
          irc_write("PONG "+str2);

          return;
    }

    sscanf(str2, "%s %s", str2, str3); // split second word from rest
    sscanf(str2, "%d", servtype);

    if(member_array(servtype, reserved) != -1)
    {
        LOG(LOG_I3IRC, "Notice [IRC]: Reserved!\n"+str+"\n");

        return;
    }

    // Error messages.
    if(servtype >= 401 && servtype <= 502)
    {

        switch (servtype)
        {
            // :clarke.freenode.net 433 * <nick> :Nickname is already in use.
            case 433:
              sscanf(str3, "* %s :%s", blah1, blah2);

              irc_write("NICK i3_bridge_");
              irc_write("PRIVMSG NickServ :GHOST i3_bridge pq878ovd");
            // :clarke.freenode.net 451 * <cmd> :Register first.
            case 451:
              call_out("irc_delayed_connect1", 1);
              call_out("irc_delayed_connect2", 5);

              return;

        }

        return;
    }

    // General messages.
    if(servtype > 0 && servtype < 401)
    {
        string *parts, *tmpWho, *tmp;
        string user_chan, serv_msg, usrs;
        string me_thing;

        parts = explode(str3, " ");
        user_chan = parts[0];
        serv_msg = implode(parts[1..], " ");

        switch(servtype)
        {
            // :lem.freenode.net 353 i3_bridge = ##lpc :ico2__ kenon i3_bridge @ChanServ
            case 353:
            {
              sscanf(serv_msg, "= %*s :%s", usrs);
              tmpWho = explode(usrs, " ");

              if(!ircWhoList || !sizeof(ircWhoList)) ircWhoList = ({ });

              foreach(string usr in tmpWho)
              {

                  switch(usr[0..0])
                  {

                      case "@":
                      case "+":
                        tmp = ircWhoList - ({ usr[1..<1] });
                        ircWhoList = tmp + ({ usr[1..<1] });
                        break;
                      default:
                        tmp = ircWhoList - ({ usr });
                        ircWhoList = tmp + ({ usr });

                  }

              }

              break;
            }

        }

        if(serv_msg[0..0] == ":") serv_msg = serv_msg[1..];

        me_thing = sprintf("%c", 2);
        parts = explode(serv_msg, me_thing);
        
        if(sizeof(parts) != 1)
        {
        
          for(int i = 1; i < sizeof(parts); i += 2)
            parts[i] = "%^BOLD%^" + parts[i] + "%^RESET%^";
        
          serv_msg = implode(parts, "");
        }
        
        me_thing = sprintf("%c", 31);
        parts = explode(serv_msg, me_thing);
        
        if(sizeof(parts) != 1)
        {
        
          for(int i = 1; i < sizeof(parts); i += 2)
            parts[i] = "%^B_BLUE%^" + parts[i] + "%^RESET%^";
        
          serv_msg = implode(parts, "");
        }
    
        CHAN_D->rec_msg(
          "announce",
          "[%^MAGENTA%^IRC%^RESET%^] %^RED%^" + user_chan + "%^RESET%^: " + serv_msg + "\n"
        );

        return;
    }

    // Handle our services now.
    func = "irc_rec_" + lower_case(str2);
    if(function_exists(func, this_object()))
        call_other(this_object(), func, str1, str3);
    else
        call_other(this_object(), "irc_rec_unsupported", str2);

}

void irc_read_callback(int fd, mixed msg)
{
    string str, *parts;
    int i, sz;

    str = (string)msg;

    if(!strlen(str)) return;

    dataline += str;

    if (str[strlen(str) - 1] != '\n') return;

    parts = explode(dataline, "\n");
    dataline = "";

    i = 0; sz = sizeof(parts);

    while (i < sz)
    {
        str = trim(parts[i]);

        catch(LOG(LOG_I3IRC, "Notice [IRC]: Rec '" + str + "'\n"));

        if (str != "") irc_service_handler(str);

        i++;
    }

    if(trim(parts[0])[0..3] != "PING") idle_timer = 0;

}

/* I3 */
int i3_channel_filter(string channel)
{

    if(member_array(channel, data["i3_chans"]) == -1)
        return 0;
    else
        return 1;
}

int isAllowed(string channel, string user, int flag)
{

    if(flag)
    {

        if(member_array(channel, data["listening"]) == -1)
            data["listening"] += ({ channel });

    }
    else
    {

        if(!sizeof(CHAN_D->getTuned(channel)))
            data["listening"] -= ({ channel });

    }

    return 1;
}

int rec_msg(string chan, string usr, string msg)
{
    mixed *packets = ({ });
    string chan_msg, irc_msg;
    int lines = 15;

    if(msg[0..5] == "/last ")
    {
        sscanf(msg, "/last %d", lines);
        msg = "/last";
    }

    switch(msg) /* We could do some neat stuff here! */
    {

        case "/last":
        {
            object ob = find_player(usr);

            if(!sizeof(history[chan]))
                tell_object(ob, "I3: Channel " + chan + " has no history yet.\n");
            else
                foreach(string histLine in history[chan][(sizeof(history[chan]) - lines)..(sizeof(history[chan]) - 1)])
                    tell_object(ob, histLine);

            return 1;
        }
        case "/all":
        {
            object ob = find_player(usr);

            if(!sizeof(history[chan]))
                tell_object(ob, "I3: Channel " + chan + " has no history yet.\n");
            else
                foreach(string histLine in history[chan])
                    tell_object(ob, histLine);

            return 1;
        }

    }

    if(msg[0..0] == ":")
    {

        foreach(string i3_chan in data["irc_map_chans"][chan])
        {
            packets += ({ ({
              "channel-e",
              5,
              mud_name(),
              lower_case(usr),
              0,
              0,
              i3_chan,
              capitalize(usr) + ".freenode",
              "$N " + msg[1..<1]
            }) });
        }

        irc_msg = sprintf("%cACTION %s%c", 1, "- " + capitalize(usr) + "@" + mud_name() + " " + msg[1..<1], 1);
        msg = " " + msg[1..<1];
    }
    else
    {

        foreach(string i3_chan in data["irc_map_chans"][chan])
        {
            packets += ({ ({
              "channel-m",
              5,
              mud_name(),
              lower_case(usr),
              0,
              0,
              i3_chan,
              capitalize(usr) + ".freenode",
              msg
            }) });
        }

        irc_msg = capitalize(usr) + "@" + mud_name() + ": " + msg;
        msg = ": " + msg;
    }

    send_packets(packets);
    irc_write(sprintf("PRIVMSG %s :%s", chan, irc_msg));

    chan_msg = sprintf("[%s] %s%s\n", "%^BOLD%^" + chan + "%^RESET%^", capitalize(usr), msg);
    CHAN_D->rec_msg(chan, chan_msg);
    addHistory(chan, ({ chan_msg }) );

    return 1;
}

/* I3 Packet Handlers */
void i3_rec_startup_reply(mixed *packet)
{

    if(packet[2] != data["i3_router"][0][0])
    {
        LOG(LOG_I3IRC, "Warning [I3]: Illegal access. Not from the router.\n");
        LOG(LOG_I3IRC, "Warning [I3]: " + identify(packet) + "\n");

        return;
    }

    if(sizeof(packet) != 8)
    {
        LOG(LOG_I3IRC, "Error [I3]: We don't like startup-reply packet size. Should be 8 but is "+sizeof(packet)+"\n");
        return;
    }

    if(!sizeof(packet[6]))
    {
        LOG(LOG_I3IRC, "Error [I3]: We don't like the absence of packet element 6.\n");
        return;
    }

    reconnect_wait = 10;

    if(packet[6][0][0] == data["i3_router"][0][0])
    {
        data["i3_router"] = packet[6];
        data["i3_router_password"] = packet[7];
    }
    else
    {
        data["i3_router"] = packet[6];

        /* Connect to the new server */
        i3_remove();
        i3_reconnect();
        save_object(datafile);
        
        return;
    }

    LOG(LOG_I3IRC, "Success [I3]: Connection established to I3 network.\n");

    save_object(datafile);
}

void i3_rec_mudlist(mixed *packet)
{

    if(sizeof(packet) != 8)
    {
        LOG(LOG_I3IRC, "Error [I3]: We don't like mudlist packet size. Should be 8 but is "+sizeof(packet)+"\n");
        return;
    }

    if(packet[6] == data["i3_mudlist_id"])
        LOG(LOG_I3IRC, "Warning [I3]: We don't like packet element 6 ("+packet[6]+"). It is the same as the current one. Continuing anyway.\n");

    if(packet[2] != data["i3_router"][0][0])
    {
        LOG(LOG_I3IRC, "Error [I3]: We don't like packet element 2 ("+packet[2]+"). It should be "+data["i3_router"][0][0]+"\n");
        return;
    }

    data["i3_mudlist_id"] = packet[6];

    mudlist_buffer += packet[7];

    save_object(datafile);
}

void i3_rec_chanlist_reply(mixed *packet)
{

    if(packet[2] != data["i3_router"][0][0]) return;

    data["i3_chanlist_id"] = packet[6];

    foreach(mixed key, mixed val in packet[7])
    {

        if(!val && sizeof(keys(data["i3_chanlist"])) && sizeof(data["i3_chanlist"][key]))
        {
            LOG(LOG_I3IRC, "Notice [I3]: Deleting channel '" + key + "' from the chanlist.\n");
            map_delete(data["i3_chanlist"], key);
        } else if(val) {

            if(sizeof(keys(data["i3_chanlist"])))
            {

                if(sizeof(data["i3_chanlist"][key]))
                    LOG(LOG_I3IRC, "Notice [I3]: Updating data for channel '" + key + "' in the chanlist.\n");
                else
                    LOG(LOG_I3IRC, "Notice [I3]: Adding channel '" + key + "' to the chanlist.\n");

                data["i3_chanlist"] += ([ key: val ]);
            } else {
                LOG(LOG_I3IRC, "Notice [I3]: Adding channel '" + key + "' to the chanlist.\n");
                data["i3_chanlist"] = ([ key: val ]);
            }

        }

        if(!i3_channel_filter(key)) continue;

        i3_send_channel_listen(key, 1);
    }

    save_object(datafile);
}

void i3_rec_error(mixed *packet)
{
    LOG(LOG_I3IRC, "Error [I3]: [" + packet[6] + "]: " + packet[7] +"\n" + sprintf("%O", packet[8]) + "\n");
}

/* I3 Channel Service Implementation */

void i3_rec_channel_m(mixed *packet)
{
    string message;

    if(packet[2] == mud_name()) return;
    if(lower_case(packet[3]) == lower_case(data["i3_nick"])) return;
    if(member_array(packet[6], keys(data["i3_map_chans"])) == -1) return;

    message = sprintf("%s." + I3_ROUTER_NAME[1..<1] + "@%s: %s", packet[7], packet[2], packet[8]);

    foreach(string irc_chan in data["i3_map_chans"][packet[6]])
    {
        string chan_msg;

        irc_write(sprintf("PRIVMSG %s :%s", irc_chan, message));

        chan_msg = sprintf("[%s] %s\n", "%^BOLD%^" + irc_chan + "%^RESET%^", message);
        CHAN_D->rec_msg(irc_chan, chan_msg);
        addHistory(irc_chan, ({ chan_msg }) );
    }

}

void i3_rec_channel_e(mixed *packet)
{
    string message;

    if(packet[2] == mud_name()) return;
    if(lower_case(packet[3]) == lower_case(data["i3_nick"])) return;
    if(member_array(packet[6], keys(data["i3_map_chans"])) == -1) return;

    message = packet[8];
    message = replace_string(message, "$N", sprintf("%s." + I3_ROUTER_NAME[1..<1] + "@%s", packet[7], packet[2]));

    foreach(string irc_chan in data["i3_map_chans"][packet[6]])
    {
        string chan_msg, irc_msg;

        irc_msg = sprintf("%cACTION %s%c", 1, "- " + message, 1);
        irc_write(sprintf("PRIVMSG %s :%s", irc_chan, irc_msg));

        chan_msg = sprintf("[%s] %s\n", "%^BOLD%^" + irc_chan + "%^RESET%^", message);
        CHAN_D->rec_msg(irc_chan, chan_msg);
        addHistory(irc_chan, ({ chan_msg }) );
    }

}

void i3_rec_channel_t(mixed *packet)
{
    string message;

    if(packet[2] == mud_name()) return;
    if(lower_case(packet[3]) == lower_case(data["i3_nick"])) return;
    if(member_array(packet[6], keys(data["i3_map_chans"])) == -1) return;

    if(find_player(packet[8]) && packet[7] == mud_name() )
    {
        message = packet[10];
        message = replace_string(message, "$N", sprintf("%s." + I3_ROUTER_NAME[1..<1] + "@%s", packet[11], packet[2]));
        message = replace_string(message, "$O", packet[12]);
    }
    else
    {
        message = packet[9];
        message = replace_string(message, "$N", sprintf("%s." + I3_ROUTER_NAME[1..<1] + "@%s", packet[11], packet[2]));
        message = replace_string(message, "$O", packet[12] + "@" + packet[7]);
    }

    foreach(string irc_chan in data["i3_map_chans"][packet[6]])
    {
        string chan_msg;

        irc_write(sprintf("PRIVMSG %s :%s", irc_chan, message));

        chan_msg = sprintf("[%s] %s\n", "%^BOLD%^" + irc_chan + "%^RESET%^", message);
        CHAN_D->rec_msg(irc_chan, chan_msg);
        addHistory(irc_chan, ({ chan_msg }) );
    }

}

void i3_rec_chan_user_req(mixed *packet)
{
    mixed *out_packet;
    int gender;
    object user = find_player(packet[6]);

    if(!user) return;

    if(user->query("gender") == "male") gender = 0;
    else if(user->query("gender") == "female") gender = 1;
    else gender = 2;

    out_packet = ({
      "chan-user-reply",
      5,
      mud_name(),
      0,
      packet[2],
      0,
      query_privs(user),
      capitalize(user->query_name()),
      gender,
    });

    send_packet(out_packet);
}

void i3_send_channel_listen(string chan, int flag)
{
    mixed *packet;

    if(member_array(chan, data["i3_chans"]) != -1 && !flag)
        data["i3_chans"] -= ({ chan });
    else
    if(member_array(chan, data["i3_chans"]) == -1 && flag)
        data["i3_chans"] += ({ chan });

    packet = ({
      "channel-listen",
      5,
      mud_name(),
      0,
      data["i3_router"][0][0],
      0,
      chan,
      flag
    });

    send_packet(packet);
}

/* Other I3 Packets */

string get_rank(mixed ob)
{

    if(objectp(ob))
    {

        if(adminp(ob)) return "%^BOLD%^%^RED%^Admin%^RESET%^";
        else if(devp(ob)) return "%^YELLOW%^Developer%^RESET%^";
        else return "%^GREEN%^User%^RESET%^";

    }

}

void i3_rec_who_req(mixed *packet)
{
    mixed *out_packet, *bot_data, *who_data;

    bot_data = ({
      ({
        "IRC_Bridge",
        idle_timer,
        "I am an I3/IRC bridge bot. I don't really exist."
      }),
    });

    who_data = map(filter(users(), (: environment($1) :) ),
      (: ({
        capitalize($1->query_name()),
        (int)query_idle($1),
        replace_string($1->query_long(), "$N", capitalize($1->query_name())) + " [" + get_rank($1) + "]"
      }) :)
    );

    foreach(string usr in ircWhoList)
    {

        if(usr == "ChanServ" || usr == data["irc_nick"]) continue;

        who_data += ({
          ({
            usr + ".freenode",
            0,
            usr + " on irc.freenode.net [%^CYAN%^IRC User%^RESET%^]"
          }),
        });

    }

    out_packet = ({
      "who-reply",
      5,
      mud_name(),
      0,
      packet[2],
      packet[3],
      bot_data + who_data,
    });

    send_packet(out_packet);
}

void i3_rec_locate_req(mixed *packet)
{
    mixed *out_packet;
    mixed user;
    string status;

    user = find_player(packet[6]);

    if(!user)
    {
        user = call_other(FINGER_D, "get_user", packet[6]);

        if(!objectp(user)) return;

        out_packet = ({
          "locate-reply",
          5,
          mud_name(),
          0,
          packet[2],
          packet[3],
          mud_name(),
          capitalize(user->query_name()),
          (interactive(user) ? query_idle(user) : 0),
          "Offline",
        });
        send_packet(out_packet);
        destruct(user);

        return;
    } else {
        if(!interactive(user)) status = "Link-Dead";
          else if(query_idle(user) > 60) status = "Idle";
          else status = "Online";

        out_packet = ({
          "locate-reply",
          5,
          mud_name(),
          0,
          packet[2],
          packet[3],
          mud_name(),
          capitalize(user->query_name()),
          (interactive(user) ? query_idle(user) : 0),
          (status ? status : 0),
        });
        send_packet(out_packet);

        return;
    }

}

void i3_rec_auth_mud_req(mixed *packet)
{
    mixed *out_packet;
    string orig = packet[2];
    string *session_names = keys(data["i3_session_keys_by_name"]);
    int *session_keys = keys(data["i3_session_keys_by_session"]);
    int session_key;

        if(packet[4] != mud_name()) return;

    /* Simple hash */
    session_key = 0;
    for(int i = 0; i < strlen(orig); i++) session_key += (orig[i] + ((i / 2) + 1));
    session_key = (session_key * 1000) + random(720217) + (time() ^ 0x55AA55AA);

    while(member_array(session_key, session_keys) != -1)
        session_key--;

    if(member_array(orig, session_names) != -1)
        map_delete(data["i3_session_keys_by_session"], data["i3_session_keys_by_name"][orig]["session_key"]);

    LOG(LOG_I3IRC, "Notice [I3]: Registering '"+orig+"' with session key: "+session_key+"\n");

    if(data["i3_session_keys_by_name"] != 0)
        data["i3_session_keys_by_name"][orig] = ([
          "registered": time(),
          "session_key": session_key,
        ]);
    else
        data["i3_session_keys_by_name"] = ([ orig: ([
          "registered": time(),
          "session_key": session_key,
        ]), ]);

    if(data["i3_session_keys_by_session"] != 0)
        data["i3_session_keys_by_session"][session_key] = orig;
    else
        data["i3_session_keys_by_session"] = ([ session_key: orig, ]);

    save_object(datafile);

    out_packet = ({
      "auth-mud-reply",
      5,
      mud_name(),
      (string)0,
      orig,
      (string)0,
      session_key,
    });

    send_packet(out_packet);
}

void i3_rec_tell(mixed *packet)
{
    mixed *out_packet;

    if(lower_case(packet[5]) != lower_case(data["i3_nick"]))
    {
        string ircUser;

        if(sscanf(packet[5], "%s.freenode", ircUser))
        {

            if(member_array(ircUser, ircWhoList) != -1)
            {
                irc_write("PRIVMSG " + ircUser + " :" + sprintf("%s." + I3_ROUTER_NAME[1..<1] + "@%s tells you: %s\n", packet[6], packet[2], packet[7]));

                return;
            }

        }

        out_packet = ({
          "error",
          5,
          mud_name(),
          (string)0,
          packet[2],
          packet[3],
          "unk-user",
          packet[5] + " was not found!\n",
          packet,
        });

        send_packet(out_packet);

        return;
    }
    else
    if(bot_cmd(sprintf("%s@%s", packet[3], packet[2]), packet[7]))
        return;
    else
    {
        out_packet =
        ({
          ({
            "emoteto",
            5,
            mud_name(),
            lower_case(data["i3_nick"]),
            packet[2],
            packet[3],
            data["i3_nick"],
            "$N rolls it's eyes.",
          }),
          ({
            "tell",
            5,
            mud_name(),
            lower_case(data["i3_nick"]),
            packet[2],
            packet[3],
            data["i3_nick"],
            "I am a bot.",
          }),
        });

        send_packets(out_packet);

        return;
    }

}

void i3_rec_emoteto(mixed *packet)
{
    mixed *out_packet;

    if(lower_case(packet[5]) != lower_case(data["i3_nick"]))
    {
        out_packet = ({
          "error",
          5,
          mud_name(),
          (string)0,
          packet[2],
          packet[3],
          "unk-user",
          packet[5] + " was not found!\n",
          packet,
        });
        send_packet(out_packet);

        return;
    } else {
        out_packet =
        ({
          ({
            "emoteto",
            5,
            mud_name(),
            lower_case(data["i3_nick"]),
            packet[2],
            packet[3],
            data["i3_nick"],
            "$N rolls it's eyes.",
          }),
          ({
            "tell",
            5,
            mud_name(),
            lower_case(data["i3_nick"]),
            packet[2],
            packet[3],
            data["i3_nick"],
            "I am a bot.",
          }),
        });
        send_packets(out_packet);

        return;
    }

}

void i3_rec_unsupported(mixed *packet)
{
    LOG(LOG_I3IRC, "Warning [I3]: Recieved unsupported msg: "+sprintf("%O", packet)+"\n");

    send_packet( ({
      "error",
      5,
      mud_name(),
      0,
      packet[2],
      packet[3],
      "not-imp",
      "Service is not implemented.",
      packet,
    }) );
}

/* IRC helpers */

void irc_chan_msg(string chan, string who, string msg)
{

    if(msg == "") return;
    if(who == data["irc_nick"]) return;
    if(member_array(chan, keys(data["irc_map_chans"])) == -1) return;

    if(member_array(chan, data["irc_chans"]) != -1)
    {
        string chan_msg;

        foreach(string i3_chan in data["irc_map_chans"][chan])
        {
            send_packet( ({
              "channel-m",
              5,
              mud_name(),
              who + ".freenode!" + lower_case(data["i3_nick"]),
              0,
              0,
              i3_chan,
              who + ".freenode",
              msg
            }) );
        }

        chan_msg = sprintf("[%s] %s: %s\n", "%^BOLD%^" + chan + "%^RESET%^", who, msg);
        CHAN_D->rec_msg(chan, chan_msg);
        addHistory(chan, ({ chan_msg }) );
    }

}

void irc_chan_emote(string chan, string who, string msg)
{

    if(msg == "") return;
    if(who == data["irc_nick"]) return;
    if(member_array(chan, keys(data["irc_map_chans"])) == -1) return;

    if(member_array(chan, data["irc_chans"]) != -1)
    {
        string chan_msg;

        foreach(string i3_chan in data["irc_map_chans"][chan])
        {
            send_packet( ({
              "channel-e",
              5,
              mud_name(),
              who + ".freenode!" + lower_case(data["i3_nick"]),
              0,
              0,    
              i3_chan,
              who + ".freenode",
              "$N " + msg
            }) );
        }

        chan_msg = sprintf("[%s] %s %s\n", "%^BOLD%^" + chan + "%^RESET%^", who, msg);
        CHAN_D->rec_msg(chan, chan_msg);
        addHistory(chan, ({ chan_msg }) );
    }

}

void irc_priv_msg(string from, string to, string msg)
{
    string me_thing, tmp;
    int ret;

    if (msg == "") return;

    me_thing = sprintf("%c", 1);

    ret = sscanf(msg, me_thing+"%s"+me_thing, tmp);

    if (ret == 1)
    {

        if (tmp[0..3] == "PING")
        {
            LOG(LOG_I3IRC, "Notice [IRC]: PING!\n");
            irc_write(sprintf("NOTICE %s :%c%s", from, 1, tmp));
        }
        else if (tmp == "VERSION")
        {
            LOG(LOG_I3IRC, "Notice [IRC]: VERSION requested.\n");
            irc_write(sprintf("NOTICE %s :%cVERSION %s", from, 1, data["realname"]));
        }
        else if (tmp == "TIME")
        {
            LOG(LOG_I3IRC, "Notice [IRC]: TIME requested.\n");
            irc_write(sprintf("NOTICE %s :%cTIME %s", from, 1, ctime(time())));
        }

    }
    else
    {
        if(bot_cmd(from, msg)) return;

        irc_write(sprintf("PRIVMSG %s :%c%s%c", from, 1, "ACTION rolls it's eyes", 1));
        irc_write(sprintf("PRIVMSG %s :%s", from, "I am a bot."));
    }

    return;
}

void irc_priv_emote(string from, string to, string msg)
{
    irc_write(sprintf("PRIVMSG %s :%c%s%c", from, 1, "ACTION rolls it's eyes", 1));
    irc_write(sprintf("PRIVMSG %s :%s", from, "I am a bot."));
}

/* IRC Handlers */

void irc_rec_privmsg(string from, string tomsg)
{
    string str1, str2;
    string blah1, blah2, blah3;
    string me_thing;
    string *parts;
    int ret;

    // got chan message, parse and use function
    sscanf(from, ":%s", str1); // cut off : in front
    sscanf(str1, "%s!%s", blah1, blah2);
    // that makes blah1 the nick, blah2 the IP
    sscanf(tomsg, "%s %s", blah3, str2);
    // that cuts off chan name and makes blah3 the chan
    sscanf(str2, ":%s", str2);

    me_thing = sprintf("%c", 1);
    ret = sscanf(str2, me_thing+"ACTION %s"+me_thing, str2);

    me_thing = sprintf("%c", 2);
    parts = explode(str2, me_thing);
    
    if(sizeof(parts) != 1)
    {
    
      for(int i = 1; i < sizeof(parts); i += 2)
        parts[i] = "%^BOLD%^" + parts[i] + "%^RESET%^";
    
      str2 = implode(parts, "");
    }
    
    me_thing = sprintf("%c", 31);
    parts = explode(str2, me_thing);
    
    if(sizeof(parts) != 1)
    {
    
      for(int i = 1; i < sizeof(parts); i += 2)
        parts[i] = "%^B_BLUE%^" + parts[i] + "%^RESET%^";
    
      str2 = implode(parts, "");
    }
    
    if (blah3[0] == '#')
    {

        if (ret == 1)
            irc_chan_emote(blah3, blah1, str2); // this is a /me
        else
            irc_chan_msg(blah3, blah1, str2); // this is a channel msg

    } else {

        if (ret == 1)
            irc_priv_emote(blah1, blah3, str2); // this is a /me
        else
            irc_priv_msg(blah1, blah3, str2);

    }

}

void irc_rec_ping(string from, string tomsg)
{
  string str1, str2;
  string blah1, blah2, blah3;

  // got chan message, parse and use function
  sscanf(from, ":%s", str1); // cut off : in front
  sscanf(str1, "%s!%s", blah1, blah2);
  // that makes blah1 the nick, blah2 the IP
  sscanf(tomsg, "%s %s", blah3, str2);
  // that cuts off chan name and makes blah3 the chan
  sscanf(str2, ":%s", str2);

  irc_write("PONG "+blah1);
}

void irc_rec_wallops(string from, string tomsg)
{
  string str1, str2;
  string blah1, blah2, blah3;
  string me_thing;
  string *parts;

  // got chan message, parse and use function
  sscanf(from, ":%s", str1); // cut off : in front
  sscanf(str1, "%s!%s", blah1, blah2);
  // that makes blah1 the nick, blah2 the IP
  sscanf(tomsg, "%s %s", blah3, str2);
  // that cuts off chan name and makes blah3 the chan
  sscanf(str2, ":%s", str2);

  me_thing = sprintf("%c", 2);
  parts = explode(str2, me_thing);

  if(sizeof(parts) != 1)
  {

    for(int i = 1; i < sizeof(parts); i += 2)
      parts[i] = "%^BOLD%^" + parts[i] + "%^RESET%^";

    str2 = implode(parts, "");
  }

  me_thing = sprintf("%c", 31);
  parts = explode(str2, me_thing);

  if(sizeof(parts) != 1)
  {

    for(int i = 1; i < sizeof(parts); i += 2)
      parts[i] = "%^B_BLUE%^" + parts[i] + "%^RESET%^";

    str2 = implode(parts, "");
  }

  CHAN_D->rec_msg(
    "announce",
    "[%^MAGENTA%^IRC%^RESET%^] %^RED%^" + blah1 + "%^RESET%^: " + str2 + "\n"
  );
}

void irc_rec_notice(string from, string tomsg)
{
  string str1, str2;
  string blah1, blah2, blah3;
  string me_thing;
  string *parts;

  // got chan message, parse and use function
  sscanf(from, ":%s", str1); // cut off : in front
  sscanf(str1, "%s!%s", blah1, blah2);
  // that makes blah1 the nick, blah2 the IP
  sscanf(tomsg, "%s %s", blah3, str2);
  // that cuts off chan name and makes blah3 the chan
  sscanf(str2, ":%s", str2);

  me_thing = sprintf("%c", 2);
  parts = explode(str2, me_thing);

  if(sizeof(parts) != 1)
  {

    for(int i = 1; i < sizeof(parts); i += 2)
      parts[i] = "%^BOLD%^" + parts[i] + "%^RESET%^";

    str2 = implode(parts, "");
  }

  me_thing = sprintf("%c", 31);
  parts = explode(str2, me_thing);

  if(sizeof(parts) != 1)
  {

    for(int i = 1; i < sizeof(parts); i += 2)
      parts[i] = "%^B_BLUE%^" + parts[i] + "%^RESET%^";

    str2 = implode(parts, "");
  }

  CHAN_D->rec_msg(
    "announce",
    "[%^MAGENTA%^IRC%^RESET%^] %^RED%^" + blah1 + "%^RESET%^: " + str2 + "\n"
  );
}

void irc_rec_mode(string from, string tomsg)
{
  string str1, str2, str3;
  string blah1, blah2, blah3;

  // got chan message, parse and use function
  sscanf(from, ":%s", str1); // cut off : in front

  if(!sscanf(str1, "%s!%s", blah1, blah2)) blah1 = str1;
  // that makes blah1 the nick, blah2 the IP
  sscanf(tomsg, "%s %s", blah3, str2);
  // that cuts off chan name and makes blah3 the chan

  if(str2[0..0] == ":")
  {
    str2 = str2[1..];

    CHAN_D->rec_msg(
      "announce",
      "[%^MAGENTA%^IRC%^RESET%^] %^RED%^" + blah1 + "%^RESET%^: Mode set for " + blah3 + " to " + str2 + "\n"
    );
  }
  else
  {
    sscanf(str2, "%s %s", str2, str3);

    CHAN_D->rec_msg(
      "announce",
      "[%^MAGENTA%^IRC%^RESET%^] %^RED%^" + blah1 + "%^RESET%^: Mode set for " + str3 + " to " + str2 + " on " + blah3 + "\n"
    );
  }

}

void irc_rec_join(string from, string tomsg)
{
  string str1, str2;
  string blah1, blah2;
  string me_thing;
  string *parts, *tmp;

  // got chan message, parse and use function
  sscanf(from, ":%s", str1); // cut off : in front
  sscanf(str1, "%s!%s", blah1, blah2);
  // that makes blah1 the nick, blah2 the IP
  sscanf(tomsg, ":%s", str2);

  switch(blah1[0..0])
  {

    case "@":
    case "+":
      tmp = ircWhoList - ({ blah1[1..<1] });
      ircWhoList = tmp + ({ blah1[1..<1] });
      break;
    default:
      tmp = ircWhoList - ({ blah1 });
      ircWhoList = tmp + ({ blah1 });

  }

  me_thing = sprintf("%c", 2);
  parts = explode(str2, me_thing);

  if(sizeof(parts) != 1)
  {

    for(int i = 1; i < sizeof(parts); i += 2)
      parts[i] = "%^BOLD%^" + parts[i] + "%^RESET%^";

    str2 = implode(parts, "");
  }

  me_thing = sprintf("%c", 31);
  parts = explode(str2, me_thing);

  if(sizeof(parts) != 1)
  {

    for(int i = 1; i < sizeof(parts); i += 2)
      parts[i] = "%^B_BLUE%^" + parts[i] + "%^RESET%^";

    str2 = implode(parts, "");
  }

  CHAN_D->rec_msg(
    "announce",
    "[%^MAGENTA%^IRC%^RESET%^] %^RED%^" + blah1 + "%^RESET%^ has joined " + str2 + "\n"
  );
}

void irc_rec_part(string from, string tomsg)
{
  string str1, str2;
  string blah1, blah2, blah3;
  string me_thing;
  string *parts;

  // got chan message, parse and use function
  sscanf(from, ":%s", str1); // cut off : in front
  sscanf(str1, "%s!%s", blah1, blah2);
  // that makes blah1 the nick, blah2 the IP
  sscanf(tomsg, "%s %s", blah3, str2);
  // that cuts off chan name and makes blah3 the chan
  sscanf(str2, ":%s", str2);

  switch(blah1[0..0])
  {

    case "@":
    case "+":
      ircWhoList -= ({ blah1[1..<1] });
      break;
    default:
      ircWhoList -= ({ blah1 });

  }

  if(str2 != "")
  {
    me_thing = sprintf("%c", 2);
    parts = explode(str2, me_thing);

    if(sizeof(parts) != 1)
    {

      for(int i = 1; i < sizeof(parts); i += 2)
        parts[i] = "%^BOLD%^" + parts[i] + "%^RESET%^";

      str2 = implode(parts, "");
    }

    me_thing = sprintf("%c", 31);
    parts = explode(str2, me_thing);

    if(sizeof(parts) != 1)
    {

      for(int i = 1; i < sizeof(parts); i += 2)
        parts[i] = "%^B_BLUE%^" + parts[i] + "%^RESET%^";

      str2 = implode(parts, "");
    }

    str2 = " (" + str2 + ")";
  }

  CHAN_D->rec_msg(
    "announce",
    "[%^MAGENTA%^IRC%^RESET%^] %^RED%^" + blah1 + "%^RESET%^ has left " + blah3 + str2 + "\n"
  );
}

void irc_rec_quit(string from, string tomsg)
{
  string str1, str2;
  string blah1, blah2;
  string me_thing;
  string *parts;

  // got chan message, parse and use function
  sscanf(from, ":%s", str1); // cut off : in front
  sscanf(str1, "%s!%s", blah1, blah2);
  // that makes blah1 the nick, blah2 the IP
  sscanf(tomsg, ":%s", str2);

  switch(blah1[0..0])
  {

    case "@":
    case "+":
      ircWhoList -= ({ blah1[1..<1] });
      break;
    default:
      ircWhoList -= ({ blah1 });

  }

  me_thing = sprintf("%c", 2);
  parts = explode(str2, me_thing);

  if(sizeof(parts) != 1)
  {

    for(int i = 1; i < sizeof(parts); i += 2)
      parts[i] = "%^BOLD%^" + parts[i] + "%^RESET%^";

    str2 = implode(parts, "");
  }

  me_thing = sprintf("%c", 31);
  parts = explode(str2, me_thing);

  if(sizeof(parts) != 1)
  {

    for(int i = 1; i < sizeof(parts); i += 2)
      parts[i] = "%^B_BLUE%^" + parts[i] + "%^RESET%^";

    str2 = implode(parts, "");
  }

  CHAN_D->rec_msg(
    "announce",
    "[%^MAGENTA%^IRC%^RESET%^] %^RED%^" + blah1 + "%^RESET%^ has quit: " + str2 + "\n"
  );
}

// :eylul!n=ubuntu@81.213.222.148 NICK :ubuntu
void irc_rec_nick(string from, string tomsg)
{
  string str1, str2;
  string blah1, blah2;
  string *tmp;

  // got chan message, parse and use function
  sscanf(from, ":%s", str1); // cut off : in front
  sscanf(str1, "%s!%s", blah1, blah2);
  // that makes blah1 the nick, blah2 the IP
  sscanf(tomsg, ":%s", str2);

  switch(blah1[0..0])
  {

    case "@":
    case "+":
      ircWhoList -= ({ blah1[1..<1] });
      break;
    default:
      ircWhoList -= ({ blah1 });

  }

  switch(str2[0..0])
  {

    case "@":
    case "+":
      tmp = ircWhoList - ({ str2[1..<1] });
      ircWhoList = tmp + ({ str2[1..<1] });
      break;
    default:
      tmp = ircWhoList - ({ str2 });
      ircWhoList = tmp + ({ str2 });

  }

  tmp = ircWhoList -= ({ str2 });
  ircWhoList += ({ str2 });

  CHAN_D->rec_msg(
    "announce",
    "[%^MAGENTA%^IRC%^RESET%^] %^RED%^" + blah1 + "%^RESET%^ has changed their nick to " + str2 + "\n"
  );
}

void irc_rec_unsupported(string str)
{
    LOG(LOG_I3IRC, "Warning [IRC]: Recieved unsupported msg: "+str+"\n");
}

/* Commands */

void bot_reply(string from, string msg)
{
    mixed *packet;
    string *parts;
    string targ_user, targ_mud;
    int i, sz;

    parts = explode(msg, "\n");

    i = 0; sz = sizeof(parts);

    while (i < sz)
    {
        msg = trim(parts[i]);

        if(sscanf(from, "%s@%s", targ_user, targ_mud) != 2)
            irc_write(sprintf("PRIVMSG %s :%s", from, msg));
        else
        {
            packet = ({
              "tell",
              5,
              mud_name(),
              lower_case(data["i3_nick"]),
              targ_mud,
              targ_user,
              data["i3_nick"],
              msg,
            });

            send_packet(packet);
        }

        i++;
    }

}

int bot_cmd_add(string from, string args)
{
    string net, chan, maps, *chanmaps;
    string check_user;

    check_user = lower_case(from);

    if(member_array(check_user, keys(registered)) == -1)
    {
        bot_reply(from, "You need to be a registered user. Use !register.\n");
        return 1;
    }
    if(member_array(check_user, identified) == -1)
    {
        bot_reply(from, "Please identify yourself. Use !identify.\n");
        return 1;
    }

    if(sscanf(args, "%s %s %s", net, chan, maps) < 3) return 0;
    net = lower_case(net);
    if(net != "i3" && net != "irc") return 0;

    chanmaps = explode(maps, ",");

    if(net == "i3")
    {
        // Handle adding of mappings from an I3 channel.

        if(member_array(chan, keys(data["i3_map_chans"])) == -1)
            data["i3_map_chans"][chan] = ({ });

        if(member_array(chan, data["i3_chans"]) == -1)
            i3_send_channel_listen(chan, 1);

        foreach(string irc_chan in chanmaps)
        {
            irc_chan = trim(irc_chan);

            if(member_array(irc_chan, data["i3_map_chans"][chan]) == -1)
            {
                irc_write("JOIN "+irc_chan);
                data["i3_map_chans"][chan] += ({ irc_chan });

                CHAN_D->registerCh("IRC", irc_chan);

                chan = replace_string(irc_chan, "#", "~");

                if(file_exists(HISTORY_FILE + "." + chan + ".o"))
                    setHistory(irc_chan, restore_variable(read_file(HISTORY_FILE + "." + chan + ".o")));
                else
                    setHistory(irc_chan, ({ }) );

            }

        }

    }
    else
    {
        // Handle adding of mappings from an IRC channel.

        if(member_array(chan, keys(data["irc_map_chans"])) == -1)
            data["irc_map_chans"][chan] = ({ });

        if(member_array(chan, data["irc_chans"]) == -1)
        {
            string irc_chan;

            irc_write("JOIN "+chan);
            data["irc_chans"][chan] += ({ chan });

            CHAN_D->registerCh("IRC", chan);

            irc_chan = replace_string(chan, "#", "~");

            if(file_exists(HISTORY_FILE + "." + irc_chan + ".o"))
                setHistory(chan, restore_variable(read_file(HISTORY_FILE + "." + irc_chan + ".o")));
            else
                setHistory(chan, ({ }) );

        }

        foreach(string i3_chan in chanmaps)
        {
            i3_chan = trim(i3_chan);

            if(member_array(i3_chan, data["irc_map_chans"][chan]) == -1)
            {
                i3_send_channel_listen(i3_chan, 1);
                data["irc_map_chans"][chan] += ({ i3_chan });
            }

        }

    }

    save_object(datafile);

    return 1;
}

int bot_cmd(string from, string cmd)
{
    string action, verb, args;
    string msg;
    int ret;

    ret = sscanf(cmd, "!%s", action);

    if(ret == 0) return 0; /* Not a special command */

    ret = sscanf(action, "%s %s", verb, args);

    if(ret == 2)
    {
        switch (verb)
        {
            case "help":
              switch (args)
              {
                case "!list":
                  msg  = "List the channel mappings between I3 and IRC\n";
                  msg += "Usage: !list\n";
                  bot_reply(from, msg);
                  return 1;
                case "!register":
                  msg  = "Register youself with the bot.\n";
                  msg += "You will need the admin password (which\n";
                  msg += "will only be given to trusted people)\n";
                  msg += "and a password you want to use.\n";
                  msg += "This password is used by the !identify command.\n";
                  msg += "Usage: !register <admin password> <your password>\n";
                  bot_reply(from, msg);
                  return 1;
                case "!identify":
                  msg  = "Identify yourself to the bot using the password\n";
                  msg += "you set up with !register.\n";
                  msg += "Usage: !identify <your password>\n";
                  bot_reply(from, msg);
                  return 1;
                case "!ban":
                  msg  = "Ban induvidual users/hostmasks/muds from\n";
                  msg += "sending messages over the bridge.\n";
                  msg += "Useful if you want to stop someone\n";
                  msg += "spamming over the bridge.\n";
                  msg += "Specifying an IRC nick will stop that\n";
                  msg += "user sending to I3, an IRC hostmask\n";
                  msg += "(such as *.lpuni.org) will stop all\n";
                  msg += "users connecting from that domain\n";
                  msg += "(it has to match what IRC sees, this\n";
                  msg += "bot will *not* do name resolving)\n";
                  msg += "sending to I3, user@mudname will ban\n";
                  msg += "that user on that mudname from sending\n";
                  msg += "to IRC and @mudname will stop all users\n";
                  msg += "on that mudname from sending to IRC.\n";
                  msg += "Usage: !ban <IRC nick>\n";
                  msg += ".......!ban <IRC hostmask>\n";
                  msg += ".......!ban <user@mudname>\n";
                  msg += ".......!ban <@mudname>\n";
                  bot_reply(from, msg);
                  return 1;
                case "!unban":
                  msg  = "Stops the bans put in place by !ban.\n";
                  msg += "Usage: !unban <IRC nick>\n";
                  msg += ".......!unban <IRC hostmask>\n";
                  msg += ".......!unban <user@mudname>\n";
                  msg += ".......!unban <@mudname>\n";
                  bot_reply(from, msg);
                  return 1;
                case "!add":
                  msg  = "Add channels to map between.\n";
                  msg += "Existing mappings will have the new\n";
                  msg += "channels appended onto them.\n";
                  msg += "Usage: !add i3 <I3 channel> <IRC channel>, ...\n";
                  msg += "Usage: !add irc <IRC channel> <I3 channel>, ...\n";
                  bot_reply(from, msg);
                  return 1;
                case "!delete":
                  msg  = "Delete mappings.\n";
                  msg += "If a mapping is empty it will be removed.\n";
                  msg += "Usage: !delete i3 <I3 channel> <IRC channel>, ...\n";
                  msg += "Usage: !delete irc <IRC channel> <I3 channel>, ...\n";
                  bot_reply(from, msg);
                  return 1;
              }

              return bot_cmd(from, "!help");
            case "reg":
            case "register":
              return bot_cmd(from, "!help !register");
            case "unreg":
            case "unregister":
              return bot_cmd(from, "!help !unregister");
            case "id":
            case "ident":
            case "identify":
              return bot_cmd(from, "!help !identify");
            case "ban":
              return bot_cmd(from, "!help !ban");
            case "unban":
              return bot_cmd(from, "!help !unban");
            case "add":
              if(bot_cmd_add(from, args)) return 1;
              return bot_cmd(from, "!help !add");
            case "del":
            case "delete":
              return bot_cmd(from, "!help !delete");
        }

        return 0;
    }

    verb = lower_case(action);

    switch (verb)
    {
        case "shutdown":
          remove();
          return 1;
        case "help":
          LOG(LOG_I3IRC, "Notice [I3IRC]: Processing !help\n");

          msg  = "!register - Register yourself with the bot. (Need admin password for this.)\n";
          msg += "!identify - Identify yourself to the bot. (Need password you setup after registering.)\n";
          msg += "!ban      - Ban an IRC user/hostmask or I3 user/mud\n";
          msg += "!add      - Add channel mappings\n";
          msg += "!delete   - Delete channel mappings\n";
          msg += "!list     - Lists channel mappings\n";

          bot_reply(from, msg);
          return 1;
        case "list":
          LOG(LOG_I3IRC, "Notice [I3IRC]: Processing !list\n");

          msg = "Intermud3 channels\n";

          foreach(string key in keys(data["i3_map_chans"]))
          {
            msg += (key+" -> ");

            foreach(string val in data["i3_map_chans"][key])
                msg += (val+" ");

            msg += "\n";
          }

          msg += "\n";
          msg += "IRC channels\n";

          foreach(string key in keys(data["irc_map_chans"]))
          {
            msg += (key+" -> ");

            foreach(string val in data["irc_map_chans"][key])
                msg += (val+" ");

            msg += "\n";
          }

          bot_reply(from, msg);
          return 1;
    }

    return 0;
}
