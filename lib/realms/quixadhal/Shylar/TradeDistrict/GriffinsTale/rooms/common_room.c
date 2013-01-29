#include <lib.h>
inherit LIB_ROOM;

varargs string format_desc(string raw, int terminal_width, int leading_indent_width, int inner_indent_width, string *highlight, string highlight_color) {
    string result = "";

    string *paragraphs = rexplode(raw, "\n");
    string leading_indent = "";
    string inner_indent = "";


    if(undefinedp(terminal_width) || terminal_width < 10) {
        object tp = this_player();

        terminal_width = 75;
        if(!undefinedp(tp)) {
            int sw = tp->GetScreen()[0];

            if(!undefinedp(sw) && sw > 0) {
                terminal_width = sw;
            }
        }
    }

    if(!undefinedp(leading_indent_width) && leading_indent_width > 0) {
        leading_indent = sprintf("%-*.*s", leading_indent_width, leading_indent_width, " ");
    }
    if(!undefinedp(inner_indent_width) && inner_indent_width > 0) {
        inner_indent = sprintf("%-*.*s", inner_indent_width, inner_indent_width, " ");
    }

    for(int i = 0; i < sizeof(paragraphs); i++) {
        string p = paragraphs[i];

        result += (i != 0 ? "" : "");
        result += leading_indent + implode(explode(sprintf("%-=*s\n", terminal_width, p), "\n"), "\n" + inner_indent);
        result += "\n";
    }

    if(!undefinedp(highlight)) {
        if(undefinedp(highlight_color)) {
            highlight_color = "%^BOLD%^%^YELLOW%^";
        }

        foreach(string h in highlight) {
            string tmp = "";

            tmp = replace_string(result, h, highlight_color + h + "%^RESET%^", 1);
            if(tmp == result) {
                if(strsrch(h, " ") != -1) {
                    string *words = explode(h, " ");

                    for(int i = 0; i < sizeof(words); i++) {
                        string t = replace_string(h, " ", "\n", i+1, i+1);
                        tmp = replace_string(result, t, highlight_color + t + "%^RESET%^", 1);
                        if(tmp != result) {
                            break;
                        }
                    }
                }
            }
            result = tmp;
        }
    }

    return result;
}

static void create() {
    string this_area = "";
    int width = 90;
    int leading = 4;
    int inner = 0;

    room::create();
    this_area = implode(rexplode(file_name(this_object()), "/")[0..<3], "/");

    SetClimate("indoors");
    SetAmbientLight(30);
    SetShort("Common Room");
    SetLong(format_desc(
            "The common room of the Griffin's Tale is a quiet place.\n"
            "A long table fills the center of this warm and comfortable "
            "room.  A bright and servicable bar occupies the east and "
            "south walls, almost entirely. A warm fireplace fills the "
            "north wall.\n"
            "A solid, if somewhat beaten, oak door leads out into the "
            "street to the west.  An open archway leads past the fireplace "
            "and into the kitchen northwards.  To the east, a set of double "
            "doors leads deeper into the inn.  Along the south wall, sits "
            "a smaller door.",
            width, leading, inner,
            ({ "long table", "bar", "fireplace", "oak door", "archway", "double doors", "smaller door" })
            ));
    SetItems( ([
                ({ "long table", "table" }) : format_desc(
                        "A long table, with many battle scars, attests to times that "
                        "were not so quiet.  Several large candelabras perch on the "
                        "tabletop, and provide much of the light here.  A few oil lamps "
                        "hang from the rafters, but do little but brighten the smoke filled "
                        "air.  The bench seat along one side is noticably shorter than "
                        "the bench on the other side.  A few smaller round tables lurk "
                        "around the less well-lit edges of the room.",
                        width, leading, inner, ({ "candelabras", "oil lamps", "smoke" })
                        ),
                ({ "candleabras", "candelabra", "candles", "candle" }) : format_desc(
                        "Several candelabras rest on the uneven and stained surface of "
                        "the long table that serves as the eating, drinking, and "
                        "fighting surface of the Griffin's Tale.\n"
                        "They are all made of a dull silver metal that probably has "
                        "quite a bit of lead in it, and while not actually bolted to "
                        "the table, they won't move without a good amount of effort.",
                        width, leading, inner
                        ),
                ({ "oil lamps", "oil lamp", "lamps", "lamp" }) : format_desc(
                        "A handful of oil lamps hang, or are bolted to, the rafters "
                        "throughout the common room.  They burn fairly cleanly, and "
                        "don't seem to gutter much, which means all the smoke must "
                        "have some other source.",
                        width, leading, inner, ({ "smoke" })
                        ),
                ({ "smoke", "air" }) : format_desc(
                        "Smoke fills the room, giving every light source a faint aura "
                        "and granting each breath a cheerful scent of cherry wood, fine "
                        "tobacco, and the undercurrent of something you can't quite "
                        "identify, but which makes your head feel light.  Of course, "
                        "the odor of cooked meats and spilled beer drifts along between "
                        "the smoke, as well.",
                        width, leading, inner, ({ "meats" })
                        ),
                ({ "meats", "meat", "food" }) : format_desc(
                        "You can't quite see what it is that smells so good, but some "
                        "creature has been given the kind of love in cooking that we "
                        "all hope for in life.  The rich scent of freshly baked bread "
                        "stands behind the roasted meat, and a rich heady beer flavor "
                        "makes you really think it's time for a hearty meal!",
                        width, leading, inner,
                        ),
                ({ "bar" }) : format_desc(
                        "The bar dominates the entire south-eastern corner, polished "
                        "mahogany and brass speak of a proud history behind the family "
                        "which runs this establishment.  Exotic bottles and skins speak "
                        "of a success which few in these parts achieve in less than a "
                        "dozen lifetimes.  Gleaming brass lamps cast a warm and even "
                        "light across the entire bar, and make the seats seem comfortable "
                        "and safe.",
                        width, leading, inner
                        ),
                ({ "fireplace", "fire" }) : format_desc(
                        "A stone fireplace stands between this room and the kitchen, to "
                        "the north.  Since it is open to both rooms, you can see a cook pot "
                        "and catch an occasional movement from the kitchen staff.  Warm furs "
                        "cover the floor in front of it, and a few chairs are placed nearby "
                        "for comfort.\n"
                        "The smell of the cherry wood smoke is comforting, and the meats "
                        "and other fine foods make you wonder if it's time for dinner yet.",
                        width, leading, inner, ({ "smoke", "meats" })
                        ),
                ({ "double doors", "hallway", "east door", "east" }) : format_desc(
                        "You can't really tell what lies beyond the simple ash doors, but "
                        "it would make sense that the private rooms of the inn would be "
                        "found through there.",
                        width, leading, inner
                        ),
                ({ "oak door", "beaten door", "door", "west" }) : format_desc(
                        "This is a heavy oak door, weathered and with quite a few cracks "
                        "and what appear to be knife marks.  It obviously leads out into "
                        "the street.",
                        width, leading, inner
                        ),
                ({ "archway", "arch", "kitchen", "north" }) : format_desc(
                        "An open archway leads around the massive stone fireplace, into "
                        "the kitchen.  It is wide enough to allow two servers to pass "
                        "each other with food laden trays, hinting that in busier times, "
                        "the common room was quite full.",
                        width, leading, inner, ({ "food" })
                        ),
                ({ "smaller door", "small door", "south door", "south" }) : format_desc(
                        "A small door hides against the south wall, almost lost behind a bar "
                        "stool, and somehow avoiding most of the light in the room.  It appears "
                        "just as solid and dark as the entry door, but has no signs of wear or "
                        "abuse.",
                        width, leading, inner
                        ),

                ]) );
    SetExits( ([
                "north" : this_area + "/rooms/kitchen",
                "south" : this_area + "/rooms/back_room",
                "east" : this_area + "/rooms/hallway1",
                "west" : "/realms/quixadhal/workroom",
                ]) );
    SetDoor("west", this_area + "/doors/common_room");
}

void init(){
    ::init();
}
