/*
 * json.c
 *
 * mixed decodeJSON(string message)
 *   Creates an LPC variable from a JSON message.
 *
 * string encodeJSON(mixed lpcvar)
 *   Creates a JSON message from an LPC variable.
 *
 */

#define DEBUG_LEVEL 0
#define D(l,x) if (((l) - (DEBUG_LEVEL)) <= 0) printf("%O\n\n", x)

#define INT "(0|[1-9][0-9]*)"
#define FRAC "[.][0-9]+"
#define EXP "[eE][+-]?[0-9]+"

#define STRING "\"([^\"])*\""
// #define NUMBER "[-]?[0-9]+([.][0-9]+)?([eE][+-]?[0-9]+)?"
#define REAL "[-]?" + INT + "(" + FRAC + ")(" + EXP + ")?"
#define INTEGER "[-]?" + INT + "(" + EXP + ")?"
#define LITERAL "(false|null|true)"
#define NAME_SEPARATOR ":"
#define VALUE_SEPARATOR ","
#define ARRAY_BEGIN "[[]"
#define ARRAY_END "[]]"
#define OBJECT_BEGIN "{"
#define OBJECT_END "}"
#define WS "( |\t|\r|\n)+"

#define TOK_DEFAULT 0
#define TOK_STRING 1
#define TOK_REAL 2
#define TOK_INTEGER 3
#define TOK_LITERAL 4
#define TOK_NAME_SEPARATOR 5
#define TOK_VALUE_SEPARATOR 6
#define TOK_ARRAY_BEGIN 7
#define TOK_ARRAY_END 8
#define TOK_OBJECT_BEGIN 9
#define TOK_OBJECT_END 10
#define TOK_WS 11

mixed decodeJSON(string message)
{
    mixed *assoc;
    string tmp = "";
    int i, sz;

    /* Preserve quotes. */
    message = replace_string(message, "\\\"", "&quot;");
    D(2, message);

    assoc = reg_assoc(
        message,
        ({
            STRING,
            REAL,
            INTEGER,
            LITERAL,
            NAME_SEPARATOR,
            VALUE_SEPARATOR,
            ARRAY_BEGIN,
            ARRAY_END,
            OBJECT_BEGIN,
            OBJECT_END,
            WS,
        }),
        ({
            TOK_STRING,
            TOK_REAL,
            TOK_INTEGER,
            TOK_LITERAL,
            TOK_NAME_SEPARATOR,
            TOK_VALUE_SEPARATOR,
            TOK_ARRAY_BEGIN,
            TOK_ARRAY_END,
            TOK_OBJECT_BEGIN,
            TOK_OBJECT_END,
            TOK_WS,
        }),
        TOK_DEFAULT
    );

    D(1, assoc);

    for (i = 0, sz = sizeof(assoc[0]) ; i < sz ; i++)
    {
        switch (assoc[1][i])
        {
            /* Here we try to clean up the integer, specifically with the exponent. */
            case TOK_INTEGER:
            {
                mixed a;
                string ts = "";
                int l, tsz;

                a = reg_assoc(
                  assoc[0][i],
                  ({ "-", INT, EXP }),
                  ({ 1, 2, 3 }),
                  0
                );

                for (l = 0, tsz = sizeof(a[0]) ; l < tsz ; l++)
                {
                    if (a[1][l] == 0) continue;

                    switch (a[1][l])
                    {
                        case 1:
                        case 2: break;
                        case 3:
                            /* LPC requires a sign for the exponent. */
                            if (regexp(a[0][l], "[eE][0-9]+"))
                            {
                                a[0][l] = replace_string(a[0][l], "e", "e+");
                                a[0][l] = replace_string(a[0][l], "E", "E+");
                            }
                            break;
                    }

                    ts += a[0][l];
                }

                assoc[0][i] = ts;
                break;
            }
            /* Here we try to clean up the real, specifically with the exponent. */
            case TOK_REAL:
            {
                mixed a;
                string ts = "";
                int l, tsz, last = 0;

                a = reg_assoc(
                  assoc[0][i],
                  ({ "-", INT, FRAC, EXP }),
                  ({ 1, 2, 3, 4 }),
                  0
                );

                for (l = 0, tsz = sizeof(a[0]) ; l < tsz ; l++)
                {
                    if (a[1][l] == 0) continue;

                    switch (a[1][l])
                    {
                        case 1:
                        case 2: break;
                        case 3:
                            /* This shouldn't trigger. */
                            if (last != 2) ts += "0";
                            break;
                        case 4:
                            /* LPC requires a sign for the exponent. */
                            if (regexp(a[0][l], "[eE][0-9]+"))
                            {
                                a[0][l] = replace_string(a[0][l], "e", "e+");
                                a[0][l] = replace_string(a[0][l], "E", "E+");
                            }
                            break;
                    }

                    ts += a[0][l];
                    last = a[1][l];
                }

                assoc[0][i] = ts;
                break;
            }
            /* No idea what to do with this currently. */
            /* Convert into a string and leave for the programmer to translate. */
            case TOK_LITERAL:
                assoc[0][i] = "\"" + assoc[0][i] + "\"";
                break;
            case TOK_ARRAY_BEGIN:
                assoc[0][i] = "({";
                break;
            /* Add a comma to pacify restore_variable() */
            case TOK_ARRAY_END:
                assoc[0][i] = ",})";
                break;
            case TOK_OBJECT_BEGIN:
                assoc[0][i] = "([";
                break;
            /* Add a comma to pacify restore_variable() */
            case TOK_OBJECT_END:
                assoc[0][i] = ",])";
                break;
            /* Basically an illegal literal so we turn it into a string. */
            case TOK_DEFAULT:
                if (assoc[0][i] != "")
                    assoc[0][i] = "\"" + assoc[0][i] + "\"";

                break;
            /* Strip whitespace. */
            case TOK_WS:
                assoc[0][i] = "";
                break;
        }

        tmp += assoc[0][i];
    }

    /* Restore quotes. */
    tmp = replace_string(tmp, "&quot;", "\\\"");
    D(2, assoc);
    D(1, tmp);
    return restore_variable(tmp);
}

string encodeJSON(mixed mixvar)
{
    int sz;
    string ret;

    if (undefinedp(mixvar)) return "null";
    if (intp(mixvar) || floatp(mixvar)) return "" + mixvar;
    if (stringp(mixvar))
    {
        mixvar = replace_string(mixvar, "\"", "\\\"");
        mixvar = "\"" + mixvar + "\"";
        mixvar = replace_string(mixvar, "\\", "\\\\");
        mixvar = replace_string(mixvar, "\\\"", "\"");
        mixvar = replace_string(mixvar, "\b", "\\b");
        mixvar = replace_string(mixvar, "" + 0x0c, "\\f");
        mixvar = replace_string(mixvar, "\n", "\\n");
        mixvar = replace_string(mixvar, "\r", "\\r");
        mixvar = replace_string(mixvar, "\t", "\\t");

        return mixvar;
    }
    if (arrayp(mixvar))
    {
        ret = "[";
        sz = sizeof(mixvar);

        for (int i = 0 ; i < sz ; i++)
        {
            if (i != 0) ret += ",";

            ret += encodeJSON(mixvar[i]);
        }

        return ret + "]";
    }
    if (mapp(mixvar))
    {
        mixed ks, vs;

        ret = "{";
        ks = keys(mixvar);
        vs = values(mixvar);
        sz = sizeof(ks);

        for (int i = 0 ; i < sz ; i++)
        {
            if (i != 0) ret += ",";

            ret += encodeJSON(ks[i]) + ":" + encodeJSON(vs[i]);
        }

        return ret + "}";
    }
    if (bufferp(mixvar))
    {
        ret = "";
        sz = sizeof(mixvar);

        for (int i = 0 ; i < sz ; i++)
        {
            if (i != 0) ret += ",";

            ret += encodeJSON(mixvar[i]);
        }

        return ret;
    }
    if (classp(mixvar))
    {
        ret = "";
        mixvar = disassemble_class(mixvar);
        sz = sizeof(mixvar);

        for (int i = 0 ; i < sz ; i++)
        {
            if (i != 0) ret += ",";

            ret += encodeJSON(mixvar[i]);
        }

        return ret;
    }
    if (objectp(mixvar))
    {
        /* What to do?
         * return "" or return encodeJSON(save_variable(mixvar))
         *
         * return "" for now.
         */
        return encodeJSON(save_variable(mixvar));
    }
    if (functionp(mixvar)) return "";

    /* Anything weird and we return null.
     * It really shouldn't get here, but you never know.
     */
    return "null";
}

void test()
{
    mixed mixvar;
    string msg;

    msg = @JSON_MSG
{
    "name": "Jack (\"Bee\") Nimble", 
    "format": {
        "type":       "rect", 
        "width":      1920, 
        "height":     1080, 
        "interlace":  false, 
        "frame rate": 24
    }
}
JSON_MSG;

    printf("%s\n\n", msg);
    mixvar = decodeJSON(msg);
    D(0, mixvar);
    printf("%s\n\n", encodeJSON(mixvar));

    msg = @JSON_MSG
{
    "Image":
    {
        "Width":  800,
        "Height": 600,
        "Title":  "View from 15th Floor",
        "Thumbnail":
        {
            "Url":    "http://www.example.com/image/481989943",
            "Height": 125,
            "Width":  "100"
        },
        "IDs": [116, 943, 234, 38793]
    }
}
JSON_MSG;

    printf("%s\n\n", msg);
    mixvar = decodeJSON(msg);
    D(0, mixvar);
    printf("%s\n\n", encodeJSON(mixvar));

    msg = @JSON_MSG
{ "x":1e-2, "y":1.0e2 }
JSON_MSG;

    printf("%s\n\n", msg);
    mixvar = decodeJSON(msg);
    D(0, mixvar);
    printf("%s\n\n", encodeJSON(mixvar));
}
