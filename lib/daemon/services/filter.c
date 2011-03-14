/*    /daemon/services/filter.c
 *    This service is used for filtered channels.
 *    You should put your own filtering logic in here!
 */

#define SERVICE_FILTER

#include <daemons.h>
#include <message_class.h>

void eventReceiveFilterRequest(mixed *packet) {
    // The actual payload to be filtered is in packet[7]
    // ({
    //     (string)   "chan-filter-req",
    //     (int)      5,
    //     (string)   originator_mudname,     // the router
    //     (string)   0,
    //     (string)   target_mudname,         // the owner/host mud
    //     (string)   0,
    //     (string)   channel_name,
    //     (mixed *)  packet_to_filter,
    // })
    //
    // Where packet_to_filter is a channel-m, channel-e or channel-t packet. 

    PING_D->SetOK();
    tn("eventReceiveFilterRequest: "+identify(packet),"green");
    if( file_name(previous_object()) != INTERMUD_D ) return;

    if( packet[4] != mud_name() ) return;
    // We only want to respond to packets that were sent to us for filtering.

    if( packet[7][0] != "channel-m" ) return;
    // Further, we only want regular messages, no emotes!

    if( packet[7][2] != mud_name() ) return;
    // And we only want to deal with packets which came from someone here!


    // ({
    //     (string)   "chan-filter-reply",
    //     (int)      5,
    //     (string)   originator_mudname,    // The channel host/owner mudname
    //     (string)   0,
    //     (string)   target_mudname,        // the router
    //     (string)   0,
    //     (string)   channel_name,
    //     (mixed *)  filtered_packet,
    // })

    INTERMUD_D->eventWrite(({ "chan-filter-reply", 5, mud_name(), 0, packet[2],
                    0, packet[6], packet[7] }));
}

