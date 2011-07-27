#include <lib.h>
#include <message_class.h>

inherit LIB_DAEMON;

int cmd(string args) {
    if( !args || args == "" ) {
        write(
            "%^RED%^RED\t%%^^RED%%^^\t\t%^BOLD%^%%^^BOLD%%^^%%^^RED%%^^%^RESET%^\n"
            "%^GREEN%^GREEN\t%%^^GREEN%%^^\t%^BOLD%^%%^^BOLD%%^^%%^^GREEN%%^^%^RESET%^\n"
            "%^ORANGE%^ORANGE\t%%^^ORANGE%%^^\t%^BOLD%^%%^^BOLD%%^^%%^^ORANGE%%^^%^RESET%^\n"
            "%^YELLOW%^YELLOW\t%%^^YELLOW%%^^\t%^BOLD%^%%^^BOLD%%^^%%^^YELLOW%%^^%^RESET%^\n"
            "%^BLUE%^BLUE\t%%^^BLUE%%^^\t%^BOLD%^%%^^BOLD%%^^%%^^BLUE%%^^%^RESET%^\n"
            "%^CYAN%^CYAN\t%%^^CYAN%%^^\t%^BOLD%^%%^^BOLD%%^^%%^^CYAN%%^^%^RESET%^\n"
            "%^MAGENTA%^MAGENTA\t%%^^MAGENTA%%^^\t%^BOLD%^%%^^BOLD%%^^%%^^MAGENTA%%^^%^RESET%^\n"
            "%^BLACK%^BLACK\t%%^^BLACK%%^^\t%^BOLD%^%%^^BOLD%%^^%%^^BLACK%%^^%^RESET%^\n"
            "%^WHITE%^WHITE\t%%^^WHITE%%^^\t%^BOLD%^%%^^BOLD%%^^%%^^WHITE%%^^%^RESET%^\n"
            "%^BLACK%^B_RED%^B_RED\t\t\t%%^^B_RED%%^^%^RESET%^\n"
            "%^BLACK%^%^B_GREEN%^B_GREEN\t\t\t%%^^B_GREEN%%^^%^RESET%^\n"
            "%^BLACK%^%^B_ORANGE%^B_ORANGE\t\t%%^^B_ORANGE%%^^%^RESET%^\n"
            "%^BLACK%^%^B_YELLOW%^B_YELLOW\t\t%%^^B_YELLOW%%^^%^RESET%^\n"
            "%^BLACK%^%^B_BLUE%^B_BLUE\t\t\t%%^^B_BLUE%%^^%^RESET%^\n"
            "%^BLACK%^%^B_CYAN%^B_CYAN\t\t\t%%^^B_CYAN%%^^%^RESET%^\n"
            "%^BLACK%^%^B_MAGENTA%^B_MAGENTA\t\t%%^^B_MAGENTA%%^^%^RESET%^\n"
            "%^BOLD%^%^BLACK%^%^B_BLACK%^B_BLACK\t\t\t%%^^B_BLACK%%^^%^RESET%^\n"
            "%^BLACK%^%^B_WHITE%^B_WHITE\t\t\t%%^^B_WHITE%%^^%^RESET%^\n"
            "Special tags: %%^^BOLD%%^^ and %%^^FLASH%%^^ and %%^^RESET%%^^\n\n"
            "You can mix and match, for example: \n"
            "%%^^B_RED%%^^%%^^CYAN%%^^%%^^BOLD%%^^%%^^FLASH%%^^Foo!%%^^RESET%%^^:"
            "%^B_RED%^%^CYAN%^%^BOLD%^%^FLASH%^Foo!%^RESET%^" 
        );
    } else if( args == "xterm" ) {
        string output = "";
        string xterm = "";
        int cols = 6;
        int i;

        for(i = 0; i < 16; i++) {
            if( i > 0 && !(i % cols) ) {
                output += xterm + "\n";
                xterm = "";
            }
            xterm += sprintf("%%^XTERM:%02x%%^XTERM:%02x %%^RESET%%^", i, i);
        }
        output += xterm + "\n\n";
        xterm = "";

        for(i = 16; i < 232; i++) {
            if( (i-16) > 0 && !((i-16) % cols) ) {
                output += xterm + "\n";
                xterm = "";
            }
            xterm += sprintf("%%^XTERM:%02x%%^XTERM:%02x %%^RESET%%^", i, i);
        }
        output += xterm + "\n\n";
        xterm = "";

        for(i = 232; i < 256; i++) {
            if( (i-232) > 0 && !((i-232) % cols) ) {
                output += xterm + "\n";
                xterm = "";
            }
            xterm += sprintf("%%^XTERM:%02x%%^XTERM:%02x %%^RESET%%^", i, i);
        }
        output += xterm + "\n\n";
        xterm = "";
        write(output);
    } else {
        string converted;
        int i, st, et;

        st = eval_cost();
        i = time_expression( converted = "/lib/interface"->rgb2xterm256(args));
        et = eval_cost();
        write(sprintf("%s converts to %%^%s%%^%s %%^RESET%%^\n", args, converted, converted));
        write(sprintf("Evaluation took %d microseconds (eval cost %d).\n", i, st - et));
    }
    return 1;
}

string GetHelp() {
    return ("Syntax: colors\n\n"
            "Lists all available colors in the corresponding color.");
}
