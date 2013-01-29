#include <lib.h>

inherit LIB_DOOR;

static void create() {
    door::create();

    SetSide("west", (["id" : ({ "worn wooden door", "wooden door", "door" }),
                "short" : "a door leading west",
                "long" : "This is a worn wooden door.",
                "lockable" : 1 ]) );
    SetKeys("west", ({ "common room key" }));

    SetSide("east", (["id" : ({ "door leading into the inn", "worn wooden door", "wooden door", "door" }),
                "short" : "a door leading east",
                "long" : "This is the worn wooden door of the inn.",
                "lockable" : 1 ]) );
    SetKeys("east", ({ "common room key" }));

    SetClosed(1);
    SetLocked(0);
}

void init(){
    ::init();
}
