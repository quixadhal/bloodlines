
#include <lib.h>
#include <daemons.h>

inherit LIB_DAEMON;

mixed cmd(string str) {
    if (sizeof(str) == 2)
        return CHAT_D->setSpeakerColor(str[0], str[1]);

    return CHAT_D->showSpeakerColors(str);
}


