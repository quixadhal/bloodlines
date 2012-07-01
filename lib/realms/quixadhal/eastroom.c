#include <lib.h>
#include ROOMS_H
#include "/realms/quixadhal/area/customdefs.h"

inherit LIB_ROOM;
int privacy = 0;
string privs = "";
string LongString(){
    string ret = "You are in the East Testroom of " + privs + ".\n";
    ret += "Another room lies west.";
    if(privacy) ret += "\n%^YELLOW%^There is a privacy force field "+
        "active around this room.%^RESET%^";
    return ret;
}

static void create() {
    room::create();
    privs = query_privs();
    if( privs ) privs = capitalize(privs);
    else privs = "a creator";

    SetClimate("indoors");
    SetAmbientLight(40);
    SetShort("East Test");
    SetLong( (: LongString :) );
    SetExits( ([
        "west" : "/realms/quixadhal/middleroom.c",
      ]) );
    SetDoor( "west" , "/domains/default/doors/steel_door.c" );

    SetNoModify(0);
}

void init(){
    ::init();
    add_action("mod_privacy","privacy");
    add_action("mod_privacy","priv");
}

int CanReceive(object sneak) {
    int ret;
    object *living_stack;
    if(!privacy) return ::CanReceive(sneak);
    living_stack = get_livings(sneak);
    if(!living_stack || !arrayp(living_stack)) living_stack = ({ sneak });
    foreach(object ob in living_stack){
        if(!archp(ob) && ob->GetKeyName() != lower_case(privs)){
            message("info","You bounce off the privacy field.", ob);
            message("info",ob->GetName()+" bounced off the privacy field.",
              this_object());
            if(!environment(ob)) ob->eventMoveLiving(ROOM_START);
            return 0;
        }
    }
    ret = ::CanReceive(sneak);
    if(ret){
        tell_room(this_object(), "\n\nPRIVACY WARNING: "+identify(sneak)+
          " has entered the room.\n\n", sneak);
    }
    return ret;
}

static int set_privacy(int i){
    object *npcs = filter(deep_inventory(this_object()),
      (: living($1) && !interactive($1) :) );
    privacy=i;
    SetProperty("no peer", i);
    if(sizeof(npcs)){
        foreach(object npc in npcs){
            tell_room(this_object(),"Ejecting "+identify(npc),npc);
            npc->eventMove(ROOM_FURNACE);
        }
    }
    return i;
}

int mod_privacy(string str){
    if(!archp(this_player()) &&
      this_player()->GetKeyName() != lower_case(privs)) {
        write("You lack the adequate privileges to do that.");
        tell_room(this_object(), this_player()->GetName()+" is "+
          "trying to muck around with the privacy field system.");
        return 1;
    }
    if(!str || str == ""){
        if(privacy) str = "off";
        else str = "on";
    }
    if(str=="on" || str == "1"){
        set_privacy(1);
        write("You enable the privacy field.\n");
        say(this_player()->GetName()+" enables a privacy force field "
          "around the room.");
        return 1;
    }
    if(str=="off" || str == "0"){
        set_privacy(0);
        write("You disable the privacy field.\n");
        say(this_player()->GetName()+" disables a privacy force field "
          "around the room.");
        return 1;
    }
}
