
#include <lib.h>

inherit LIB_DAEMON;

mixed cmd(string str) {
    string passwd = "";
    string charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

    for(int i = 0; i < 16; i++) {
        passwd += explode(charset, "")[random(strlen(charset))];
    }
    return passwd;
}

