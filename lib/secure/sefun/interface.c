/*    /adm/sefun/interface.c
 *    from Dead Souls
 *    user interface sefuns
 *    created by Descartes of Borg 940215
 */

#include <daemons.h>
#define SEFUN_C 1
#include <pinkfish.h>

string strip_rgb(string msg) {
    string *parts;
    string *newparts = ({ });
    int skip = 0;

    if( !msg || msg == "" ) return msg;
    parts = rexplode(msg, "%^");
    for(int i = 0; i < sizeof(parts); i++) {
        string chunk = parts[i];
        if(strlen(chunk) == 7 && chunk[0] == '#') {
            skip++;
        } else {
            if(skip > 0) {
                newparts[-1] += parts[i];
                skip = 0;
            } else {
                newparts += ({ parts[i] });
            }
        }
    }
    msg = implode(newparts, "%^");

    return msg;
}

string strip_raw_ansi(string str) {
    mixed stuff;
    string ret = "";

    stuff = pcre_assoc( str, ({ "(\e\[[0-9]+m)", "(\e\[[0-9]+\;[0-9]+m)" }), ({ 1, 2 }), 0);
    for(int i = 0; i < sizeof(stuff[0]); i++) {
        if(!stuff[1][i])
            ret += stuff[0][i];
    }
    return ret;
}

string strip_colours(string str){
    string ret;
    ret = strip_raw_ansi(str);
    ret = strip_rgb(ret);
    ret = terminal_colour(ret, unknown_terminfo);
    return ret;
}

string strip_colors(string str){
    return strip_colours(str);
}

string strip_colours_newbutold(string str){
    string ret;
    mapping Uncolor = ([ "RESET": "\b", "BOLD": "", "FLASH":"", "BLACK":"", "RED":"",
            "BLUE":"", "CYAN":"", "MAGENTA":"", "ORANGE":"", "YELLOW":"",
            "GREEN":"", "WHITE":"", "BLACK":"", "B_RED":"", "B_ORANGE":"",
            "B_YELLOW":"", "B_BLACK":"", "B_CYAN":"","B_WHITE":"", "B_GREEN":"",
            "B_MAGENTA":"", "STATUS":"", "WINDOW":"", "INITTERM": "", "B_BLUE":"",
            "ENDTERM":""]);
    ret = terminal_colour(str, Uncolor);
    return replace_string(ret, "\b", "");
}

string strip_colors_old(string str){
    string output = "";
    string *input = explode(str,"%^");
    string *list = ({ "RED","YELLOW","BLUE","GREEN","MAGENTA","ORANGE","CYAN","BLACK","WHITE"});
    list += ({ "B_RED","B_YELLOW","B_BLUE","B_GREEN","B_MAGENTA","B_ORANGE","B_CYAN","B_BLACK","B_WHITE"});
    list += ({"BOLD","FLASH","RESET"});
    foreach(string color in list) input -= ({ color });
    output = implode(input,"");
    if(sizeof(output)) return output;
    else return "";
}
