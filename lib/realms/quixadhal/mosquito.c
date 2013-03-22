/* vim:set ft=lpc: */

#include <lib.h>

inherit LIB_SENTIENT;

int co;

int chats() {
    string *chat_msg = ({
            "You look tasty!",
            "I can't wait to drink from you.",
            "Buzz.",
            "BUZZ!",
            "Hmmmmmmmmm.",
            "BZZZZZZZZ!!!!!",
            });
    string *emote_msg = ({
            "flies around looking for something to eat.",
            "buzzes around your head.",
            });


    switch(random(20)) {
        case 0:
            eventForce("say "+chat_msg[random(sizeof(chat_msg))]);
            break;
        case 1:
            eventForce("emote "+emote_msg[random(sizeof(emote_msg))]);
            break;
        default:
            break;

    }
}

int hunt_immortals(mixed ob) {
    if( !ob || !objectp(ob) )
        return 0;
    if( wizardp(ob) ) {
        // Found a god!
        if( !co ) {
            co = call_out("god_death_1", 1, this_player());
        }
        return 0;
    }
    if( playerp(ob) && !newbiep(ob) ) {
        return 1;
    }
}

static void create() {
    sentient::create();
    SetKeyName("mosquito");
    SetId( ({ "mosquito", "bug" }) );
    SetAdjectives( ({ "bloodsucking" }) );
    SetShort("A bloodsucking mosquito");
    SetLong("She is a nasty little bug, out for blood!\n");
    SetWanderSpeed(30);
    SetRace("insect");
    SetClass("fighter");
    SetGender("female");
    SetLevel(1);
    SetAction( 15, ({ (: chats :) }) );
    SetEncounter( (: hunt_immortals :) );
}

void init() {
    sentient::init();
    SetSmell( ([ "default" : "It has the metallic smell of blood." ]) );
    SetListen( ([ "default" : "It really is a very annoying high-pitched buzzing whine." ]) );
}

void god_death_1(object pl) {
    tell_room(environment(this_object()),
            "The nasty mosquito looks hungrily at the immortal " + pl->GetName() + ".\n");
    call_out("god_death_2", 5, pl);
}

void god_death_2(object pl) {
    tell_room(environment(this_object()),
            "The little bug imagines the sweet taste of " + pl->GetName() + "'s blood...\n");
    call_out("god_death_3", 4, pl);
}

void god_death_3(object pl) {
    tell_room(environment(this_object()),
            "The evil mosquito performs a kamakazi attack on the immortal "+ pl->GetName() + "\n" +
            "and gets a critical hit, slaying the mighty god even as she dies in agony!\n",
            pl);
    tell_player(pl, "Bad luck for you!  The tiny mosquito commits suicide, but she gets a critical hit\n" +
            " and takes you with her!\n");
    pl->quit();
    this_object()->remove();
}

