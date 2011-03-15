/*    /daemon/terminal.c
 *    from the Dead Souls Object Library
 *    daemon storing terminal information
 *    created by Pinkfish@Discworld
 *    rewritten for Dead Souls by Descartes of Borg 930903
 *    Version: @(#) terminal.c 1.5@(#)
 *    Last modified: 96/11/11
 */

#include <lib.h>

inherit LIB_DAEMON;

#define ANSI(p) sprintf("%c["+(p)+"m", 27)
#define ESC(p) sprintf("%c"+(p), 27)

#define TERMINAL_C
#include <pinkfish.h>

void create() {
    daemon::create();
    SetNoClean(1);
}

mapping query_term_info(string type) {
    return (term_info[type] ? term_info[type] : term_info["unknown"]);
}

string *query_terms() { return keys(term_info); }

int query_term_support(string str) {
    return (term_info[str] ? 1 : 0);
}

string no_colours(string str) {
    return terminal_colour(str, term_info["unknown"]);
}

string no_colors(string str){
    return no_colours(str);
}

string GetHTML(string str) {
    int i, tot, fcount = 0, ncount = 0;
    string tmp;

    str = terminal_colour(str, term_info["html"]);
    tmp = str;
    while( (i = strsrch(tmp, "<SPAN")) != -1 ) {
        fcount++;
        tmp = tmp[(i+5)..];
    }
    if( fcount < 1 ) {
        return str;
    }
    tmp = str;
    while( (i = strsrch(tmp, "</SPAN")) != -1 ) {
        ncount++;
        tmp = tmp[(i+6)..];
    }
    tot = fcount - ncount;
    if( tot > 0 ) {
        while( tot-- ) {
            str += "</SPAN>";
        }
    }
    return str;
}

#ifdef __DSLIB__
int GetCharmode(object ob){
    return query_charmode(ob);
}
#endif
