
#include <lib.h>
#include <daemons.h>

inherit LIB_DAEMON;

string b64chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";

string b64_encode(string s) {
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

mixed cmd(string s) {
    string b64chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    string array b;

    if (sizeof(s) < 1 )
        return "You must supply SOMETHING to encode!";

    b = rexplode( s, " " );

    if (sizeof(b) < 1)
        return "You must supply SOMETHING to encode!";

    if (b[0] == "-d") {
        // decode base64
        string r = "";

        s = implode( b[1 ..], " " );
        r = b64_decode(s);
        if (!stringp(r))
            return "Invalid input.";
        return r;
    } else {
        // encode base64
        string r = "";

        r = b64_encode(s);
        if (!stringp(r))
            return "Invalid input.";
        return r;
    }

    return 0;
}

