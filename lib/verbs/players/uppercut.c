#include <lib.h>
#include <position.h>
#include "damage_types.h"


inherit LIB_VERB;

static void create() {
    verb::create();
    SetVerb("uppercut");
    SetRules("LIV");
    SetErrorMessage("Uppercut whom?");
    SetHelp("Syntax: uppercut <LIVING>\n\n"
            "With a clenched fist, a warrior is able to deliver a powerful upwards blow that will cause his foe severe pain."
            "  The added benefit to this technique is that if the warrior is holding a weapon in his hand,"
            " the added weight of the weapon will aid in delivering an even more powerful blow.");
}

mixed can_uppercut_liv(object ob) {
    if (this_player()->GetClass() != "fighter") {
        return "Fighter only, sorry!";
    }
    return this_player()->CanManipulate();
}

//definitions
int uppercut_damage();

mixed do_uppercut_liv(object ob) {
    this_player()->SetAttack(ob);
    this_player()->eventPrint("Baam! You hit the fucker in the chin! Kidding, just testing shit Anyhow, you woulda done "+uppercut_damage()+" damage points! Good job!");
    return 1;
}

int uppercut_damage() {
    int damage_done = this_player()->GetStatLevel("strength");
    damage_done += this_player()->GetSkillLevel("melee attack");
    damage_done += random(20)-10;
    return damage_done;
}

