inherit "/std/monster";

// comment.

create() {
  set_name("mosquito");
  set_id( ({ "mosquito", "bug" }) );
  set_short("A bloodsucking mosquito");
  set_long("She is a nasty little bug, out for blood!\n");
  set("race", "insect");
  set_gender("female");
  //set_alignment(-100);
  set("aggressive", 100);
  SetWanderSpeed(30);
  SetLevel(1);
  SetMaxHealthPoints(15);
  SetHealthPoints(15);
  //set_max_mp(100);
  //set_mp(100);
  //set_overall_ac(1);
  set_skill("defense", 60);
  //set_stats("dexterity", 40);
  add_limb("head", "FATAL", GetMaxHealthPoints()/2, 0, GetLevel());
  add_limb("torso", "FATAL", GetMaxHealthPoints()+5, 0, GetLevel());
  add_limb("left front leg", "left front pincer", GetMaxHealthPoints()/3, 0, GetLevel());
  add_limb("left front pincer", "", GetMaxHealthPoints()/4, 0, GetLevel());
  add_limb("right front leg", "right front pincer", GetMaxHealthPoints()/3, 0, GetLevel());
  add_limb("right front pincer", "", GetMaxHealthPoints()/4, 0, GetLevel());
  add_limb("left centre leg", "left centre pincer", GetMaxHealthPoints()/3, 0, GetLevel());
  add_limb("left centre pincer", "", GetMaxHealthPoints()/4, 0, GetLevel());
  add_limb("right centre leg", "right centre pincer", GetMaxHealthPoints()/3, 0, GetLevel());
  add_limb("right centre pincer", "", GetMaxHealthPoints()/4, 0, GetLevel());
  add_limb("left rear leg", "left rear pincer", GetMaxHealthPoints()/3, 0, GetLevel());
  add_limb("left rear pincer", "", GetMaxHealthPoints()/4, 0, GetLevel());
  add_limb("right rear leg", "right rear pincer", GetMaxHealthPoints()/3, 0, GetLevel());
  add_limb("right rear pincer", "", GetMaxHealthPoints()/4, 0, GetLevel());
  add_limb("beak", "", GetMaxHealthPoints()/4, 0, (GetLevel() + GetLevel()/4));
  add_limb("stinger", "", GetMaxHealthPoints()/4, 0, GetLevel());
  set_fingers(2);
  set_wielding_limbs( ({ "front left pincer", "centre left pincer",
                         "front right pincer", "centre right pincer",
                         "beak", "stinger" }) );
  set_spell_chance(5);
  set_spells( ({ "missile", "shock" }) );
  set_chats( 15, ({ "Buzz.\n", "BUZZ!\n", "Hmmmmmmmm\n", "BZZZZZZZZZZ!!!\n" }));
}

void init() {
  if((int)this_player()->query_level() < 10 ) {
    if((string)this_player()->query_name() == "mosquito") return;
    this_object()->kill_ob(this_player(), 0);
  } else {
    switch((string)this_player()->query_position()) {
      case "arch":
      case "wizard":
        if(!random(20)) {
          call_out("god_death_1", 1, this_player());
        }
      break;
      default:
        say("An annoying mosquito dodges away from "+
            this_player()->query_cap_name()+".\n", this_player());
    }
  }
}

void god_death_1(object pl) {
  tell_room(environment(this_object()),
            "The nasty mosquito looks hungrily at the immortal "+
            pl->query_cap_name()+ ".\n");
  call_out("god_death_2", 5, pl);
}

void god_death_2(object pl) {
  tell_room(environment(this_object()),
            "The little bug imagines the sweet taste of "+
            pl->query_cap_name()+ "'s blood...\n");
  call_out("god_death_3", 4, pl);
}

void god_death_3(object pl) {
  tell_room(environment(this_object()),
            "The evil mosquito performs a kamakazi attack on the immortal "+
            pl->query_cap_name()+ "\nand gets a critical hit, "+
            "slaying the mighty god even as she dies in agony!\n",
            pl);
  tell_player(pl, "Bad luck for you!  The tiny mosquito "+
                  "commits suicide, but she gets a critical hit\nand "+
                  "takes you with her!\n");
  pl->quit();
  this_object()->remove();
}

mixed eventEncounter(object who) {
    if( !living(who) ) {
        return 1;
    }
    if( !query_heart_beat() ) {
        set_heart_beat(5);
    }
    if( this_object() && environment(this_object()) ) {
        if((int)who->query_level() < 10 ) {
            if((string)who->query_name() == "mosquito") return;
            this_object()->kill_ob(who, 0);
        } else {
            switch((string)this_player()->query_position()) {
                case "arch":
                case "wizard":
                    if(!random(20)) {
                        call_out("god_death_1", 1, this_player());
                    }
                    break;
                default:
                    say("An annoying mosquito dodges away from "+
                        this_player()->query_cap_name()+".\n", this_player());
            }
        }
    }
    return 1;
}

/*
void catch_tell(string str) {
  object ob;
  string who;

  if(!interact("enters", str)) return;
  sscanf(str, "%s enters%*s", who);
  who = lower_case(who);
  ob= present(who, environment(this_object()));
  if(!ob) {
    if(!interact("appears", str)) return;
    sscanf(str, "%s appears%*s", who);
    who = lower_case(who);
    ob= present(who, environment(this_object()));
  }
  if(!ob) return;
  if((int)ob->query_level() < 10 ) {
    if((string)ob->query_name() == "mosquito") return;
    this_object()->kill_ob(ob, 0);
  } else {
    switch((string)this_player()->query_position()) {
      case "arch":
      case "wizard":
        if(!random(20)) {
          call_out("god_death_1", 1, this_player());
        }
        break;
      default:
        say("An annoying mosquito dodges away from "+
            this_player()->query_cap_name()+".\n", this_player());
    }
  }
}
*/
