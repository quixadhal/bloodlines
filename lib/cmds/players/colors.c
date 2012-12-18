#include <lib.h>
#include <message_class.h>

inherit LIB_DAEMON;

int cmd(string args) {
    string output = "";
    string xterm = "";
    string bxterm = "";
    int ansi_cols = 4;
    int xterm_cols = 6;
    int grey_cols = 7;
    int i,r,g,b;
    array ansi = ({
            "BLACK", "RED", "GREEN", "ORANGE", "BLUE", "MAGENTA", "CYAN", "GREY",
            "DARKGREY", "LIGHTRED", "LIGHTGREEN", "YELLOW", "LIGHTBLUE", "PINK", "LIGHTCYAN", "WHITE",
            });
    array special = ({
            "BOLD", "FLASH", "ITALIC", "RESET", "REVERSE", "STRIKETHRU", "UNDERLINE",
            });

    output += "Pinkfish color codes are always surrounded by %%^^ tokens, like %%^^RED%%^^\n";
    output += "They are cumulative, so you can do %%^^BOLD%%^^%%^^UNDERLINE%%^^%%^^RED%%^^%^BOLD%^%^UNDERLINE%^%^RED%^fun stuff,\n";
    output += "but you should always remember to add a%^RESET%^ %%^^RESET%%^^ at the end,\n";
    output += "or colors will bleed into the next thing.\n\n";

    output += "ANSI colors:\n    ";
    for(i = 0; i < 16; i++) {
        if( i > 0 && !(i % ansi_cols) ) {
            output += xterm + "\n    ";
            xterm = "";
        }
        if(i == 0)
            xterm += sprintf("%%^B_DARKGREY%%^%%^%s%%^ %|12s %%^RESET%%^", ansi[i], ansi[i]);
        else
            xterm += sprintf("%%^%s%%^ %|12s %%^RESET%%^", ansi[i], ansi[i]);
    }
    output += xterm + "\n\n";
    xterm = "";

    output += "Background colors:\n    ";
    for(i = 0; i < 16; i++) {
        if( i > 0 && !(i % ansi_cols) ) {
            output += xterm + "\n    ";
            xterm = "";
        }
        if(i == 0)
            xterm += sprintf("%%^DARKGREY%%^%%^B_%s%%^ %|12s %%^RESET%%^", ansi[i], "B_"+ansi[i]);
        else
            xterm += sprintf("%%^BLACK%%^%%^B_%s%%^ %|12s %%^RESET%%^", ansi[i], "B_"+ansi[i]);
    }
    output += xterm + "\n\n";
    xterm = "";

    output += "Special tags:\n    ";
    for(i = 0; i < sizeof(special); i++) {
        if( i > 0 && !(i % ansi_cols) ) {
            output += xterm + "\n    ";
            xterm = "";
        }
        xterm += sprintf(" %|12s ", special[i]);
    }
    output += xterm + "\n\n";
    xterm = "";
    bxterm = "";

    if( args && args == "xterm" ) {

        output += "XTERM256 colors:                           XTERM256 background colors:\n    ";
        for(i = r = g = b = 0; r < 6 && g < 6 && b < 6; i++) {
            if( i > 0 && !(i % xterm_cols) ) {
                output += xterm + "      " + bxterm + "\n    ";
                xterm = "";
                bxterm = "";
            }
            if(r == 0 && g == 0 && b == 0)
                xterm += sprintf("%%^B_DARKGREY%%^%%^F%d%d%d%%^ F%d%d%d %%^RESET%%^", r,g,b, r,g,b);
            else
                xterm += sprintf("%%^F%d%d%d%%^ F%d%d%d %%^RESET%%^", r,g,b, r,g,b);
            bxterm += sprintf("%%^F%d%d%d%%^%%^B%d%d%d%%^ B%d%d%d %%^RESET%%^", (r+3)%6, (g+3)%6, (b+3)%6, r,g,b, r,g,b);
            b++;
            if(b > 5) { g++; b = 0; }
            if(g > 5) { r++; g = 0; b = 0; }
            if(r > 5) break;
        }
        output += xterm + "      " + bxterm + "\n\n";
        xterm = "";
        bxterm = "";

        output += "XTERM256 greyscale colors:                 XTERM256 greyscale background colors:\n    ";
        for(i = 0; i < 26; i++) {
            if( i > 0 && !(i % grey_cols) ) {
                output += xterm + "       " + bxterm + "\n    ";
                xterm = "";
                bxterm = "";
            }
            if(i == 0)
                xterm += sprintf("%%^B_DARKGREY%%^%%^G%02d%%^ G%02d %%^RESET%%^", i, i);
            else
                xterm += sprintf("%%^G%02d%%^ G%02d %%^RESET%%^", i, i);
            if(i > 13)
                bxterm += sprintf("%%^BLACK%%^%%^BG%02d%%^ BG%02d %%^RESET%%^", i, i);
            else
                bxterm += sprintf("%%^BG%02d%%^ BG%02d %%^RESET%%^", i, i);
        }
        output += xterm + "                 " + bxterm + "\n\n";
        xterm = "";
        bxterm = "";

        this_player()->eventPage(explode(output, "\n"));
    } else if(args) {
        string converted;
        int st, et;

        st = eval_cost();
        i = time_expression( converted = "/lib/interface"->rgb2xterm256(args));
        et = eval_cost();
        write(sprintf("%s converts to %%^%s%%^%s %%^RESET%%^\n", args, converted, converted));
        write(sprintf("Evaluation took %d microseconds (eval cost %d).\n", i, st - et));
    } else {
        write(output);
    }
    return 1;
}

string GetHelp() {
    return ("Syntax: colors\n\n"
            "Lists all available colors in the corresponding color.");
}
