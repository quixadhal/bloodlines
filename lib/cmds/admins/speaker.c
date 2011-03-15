/* /cmd/admins/speaker
 *
 * Command to display or adjust speaker's color in chat channels
 */

#include <lib.h>
#include <daemons.h>

inherit LIB_DAEMON;

int cmd(string str)
{
    string *args;
    string name;
    string color;

    if(!archp(previous_object())) return 0;
    if(!str || str == "")
    {
        // No arguments, so just display
        this_player()->eventPage(explode(CHAT_D->showSpeakerColors(), "\n"));
        return 1;
    } else {
        args = explode(str, ",");
        if( sizeof(args) < 2 )
        {
            // only one argument, so display who shares that name's colors
            this_player()->eventPrint(CHAT_D->showSpeakerColors(trim(str)));
            return 1;
        } else {
            // Two (or more?) arguments, so set.
            name = trim(args[0]);
            color = trim(args[1]);
            if(CHAT_D->setSpeakerColor(name, color))
            {
                write2(name + " color set to " + color + "\n");
                return 1;
            }
        }
    }
    return 0;
}

string GetHelp()
{
    return  "Syntax:  speaker [<player@mud> [, <pinkfish tag>]]\n"
            "Used to display all speaker colors (no arguments), "
            "display a single speaker's color (one argument), "
            "or set a speaker's color to a given tag.";
}

