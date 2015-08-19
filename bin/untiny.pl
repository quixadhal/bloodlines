#!/usr/bin/perl -w
# Created by Quixadhal, 2010.05.01

# Fed a URL, this script will fetch it and return information about the actual
# destination URL and, in the case of youtube, the video in question.
# A second argument is for the mud channel the URL came from, if any.
#
# <link rel="canonical" href="/watch?v=IleiqUDYpFQ">
# <link rel="alternate" media="handheld" href="http://m.youtube.com/watch?desktop_uri=%2Fwatch%3Fv%3DIleiqUDYpFQ&amp;v=IleiqUDYpFQ&amp;gl=US">
# <meta name="title" content="Ronald Reagan: First Inaugural Address (1 of 3)">
# <meta name="description" content="Senator Hatfield, Mr. Chief Justice, Mr. President, Vice President Bush, Vice President Mondale, Senator Baker, Speaker O\'Neill, Reverend Moomaw, and my fell...">
# <meta name="keywords" content="president, ronald, reagan, first, inaugural, inauguration, address, washington, 80s, reaganomics, ronny, gipper, carter, jfk, kennedy, bush, clinton, republi...">
# <meta itemprop="duration" content="PT8M26S">
#
# Here are the triggers I used in Tinyfugue, to feed it.
#
# 00:45 <intergossip> Kalinash@Fire and Ice: http://www.youtube.com/watch?v=IleiqUDYpFQ
#
#/def -mregexp -p2 -t"<([\w-]+)>.*(https?://tinyurl.com/[^&\?\.]+)" check_tiny_chan = /quote -0 url !~/bin/untiny.pl '%P2' '%P1'
#/def -mregexp -p2 -t"<([\w-]+)>.*(https?://bit.ly/[^&\?\.]+)" check_bitly_chan = /quote -0 url !~/bin/untiny.pl '%P2' '%P1'
#/def -mregexp -p2 -t"<([\w-]+)>.*(https?://goo.gl/[^&\?\.]+)" check_googl_chan = /quote -0 url !~/bin/untiny.pl '%P2' '%P1'
#/def -mregexp -p2 -t"<([\w-]+)>.*(https?://mcaf.ee/[^&\?\.]+)" check_mcafee_chan = /quote -0 url !~/bin/untiny.pl '%P2' '%P1'
#/def -mregexp -p2 -t"<([\w-]+)>.*(https?://migre.me/[^&\?\.]+)" check_migreme_chan = /quote -0 url !~/bin/untiny.pl '%P2' '%P1'
#/def -mregexp -p2 -t"<([\w-]+)>.*(https?://durl.me/[^&\?\.]+)" check_durlme_chan = /quote -0 url !~/bin/untiny.pl '%P2' '%P1'
#/def -mregexp -p2 -t"<([\w-]+)>.*(https?://is.gd/[^&\?\.]+)" check_isgd_chan = /quote -0 url !~/bin/untiny.pl '%P2' '%P1'
#/def -mregexp -p2 -t"<([\w-]+)>.*(https?://dailym.ai/[^&\?\.]+)" check_daily_chan = /quote -0 url !~/bin/untiny.pl '%P2' '%P1'
#/def -mregexp -p2 -t"<([\w-]+)>.*(https?://ebay.to/[^&\?\.]+)" check_ebay_chan = /quote -0 url !~/bin/untiny.pl '%P2' '%P1'
#/def -mregexp -p2 -t"<([\w-]+)>.*(https?://youtu.be/[^&\?\.]+)" check_yout_chan = /quote -0 url !~/bin/untiny.pl '%P2' '%P1'
#/def -mregexp -p2 -t"<([\w-]+)>.*(https?://onforb.es/[^&\?\.]+)" check_forbs_chan = /quote -0 url !~/bin/untiny.pl '%P2' '%P1'
#/def -mregexp -p2 -t"<([\w-]+)>.*(https?://www.youtube.com/watch\?v=[^&\?\.]+)" check_youtube_chan = /if (%P1 !~ "url") /quote -0 url !~/bin/untiny.pl '%P2' '%P1'%; /endif
#/def -mregexp -p2 -t"<([\w-]+)>.*(https?://imgur.com/[^&\>\.]+)" check_imgur_chan = /quote -0 url !~/bin/untiny.pl '%P2' '%P1'

use strict;
use English;
use Data::Dumper;
use HTTP::Request::Common qw(POST);
use HTML::Entities;
use LWP::UserAgent;
use URI;

my $CHATTER = '/home/bloodlines/lib/secure/save/chat.o';

sub channel_color {
    my $channel = shift;
    my %colors = (
        "intermud"    => "%^B_BLACK%^%^GREY%^",
        "muds"        => "%^B_BLACK%^%^GREY%^",
        "connections" => "%^B_BLACK%^%^WHITE%^",
        "death"       => "%^LIGHTRED%^",
        "cre"         => "%^LIGHTGREEN%^",
        "admin"       => "%^LIGHTMAGENTA%^",
        "newbie"      => "%^B_YELLOW%^%^BLACK%^",
        "gossip"      => "%^B_BLUE%^%^YELLOW%^",

        "ds"          => "%^YELLOW%^",
        "dchat"	      => "%^CYAN%^",
        "intergossip" => "%^GREEN%^",
        "intercre"    => "%^ORANGE%^",
        "pyom"        => "%^FLASH%^%^LIGHTGREEN%^",
        "free_speech" => "%^PINK%^",
        "url"         => "%^WHITE%^",

        "ibuild"      => "%^B_RED%^%^YELLOW%^",
        "ichat"       => "%^B_RED%^%^GREEN%^",
        "mbchat"      => "%^B_RED%^%^GREEN%^",
        "pchat"       => "%^B_RED%^%^LIGHTGREEN%^",
        "i2game"      => "%^B_BLUE%^",
        "i2chat"      => "%^B_GREEN%^",
        "i3chat"      => "%^B_RED%^",
        "i2code"      => "%^B_YELLOW%^%^RED%^",
        "i2news"      => "%^B_YELLOW%^%^BLUE%^",
        "imudnews"    => "%^B_YELLOW%^%^CYAN%^",
        "irc"         => "%^B_BLUE%^%^GREEN%^",
        "ifree"       => "%^B_BLUE%^%^GREEN%^",

        "default"      => "%^LIGHTBLUE%^",
        "default-IMC2" => "%^B_BLUE%^%^WHITE%^"
    );
    return $colors{default} if !defined $channel;
    return $colors{$channel} if exists $colors{$channel};
    return $colors{default};
}

sub get_url {
    my $url = shift;

    return undef if !defined $url;
    my $timeout = 90;
    my $lwp = LWP::UserAgent->new();
       $lwp->timeout($timeout/2);
       $lwp->agent("User-Agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.5) Gecko/20031007 Firebird/0.7");
       $URI::ABS_ALLOW_RELATIVE_SCHEME = 1;
       $URI::ABS_REMOTE_LEADING_DOTS = 1; 
    my $request = HTTP::Request->new(GET => $url);
    my $response = undef;

    my $given_uri = URI->new($url);
    my $given_host = $given_uri->host;
    my $origin_uri = undef;

    #print STDERR "DEBUG: given URL:  $given_uri\n";
    #print STDERR "DEBUG: given HOST: $given_host\n";

    if($request) {
        eval {
            local $SIG{ALRM} = sub { die "Exceeded Timeout of $timeout for $url\n" };
            alarm $timeout;
            $response = $lwp->request($request);
            alarm 0;
        };
        warn "Timeout" if($EVAL_ERROR and ($EVAL_ERROR =~ /^Exceeded Timeout/));

        if( (defined $response) and $response->is_success ) {
            my $origin = undef;
            for( my $prev = $response; defined $prev; $prev = $prev->previous ) {
                $origin = $prev->header("location");
                #print STDERR "DEBUG: $origin\n";
                #print STDERR "DEBUG: " . Dumper($prev) . "\n";
                last if defined $origin;
            }
            #print STDERR "DEBUG: origin URL: $origin\n" if defined $origin;
            $origin_uri = (defined $origin) ? URI->new($origin) : $given_uri->clone;
        }
        return ($origin_uri, $response->content);
    }
    return undef;
}

sub get_youtube_id {
    my $page = shift;

    $page =~ /<link\s+rel=\"canonical\"\s+href=\".*?\/watch\?v=([^\"]*)\">/;
    my ($id) =  ($1);
    return $id;
}

sub get_youtube_title {
    my $page = shift;

    $page =~ /<meta\s+name=\"title\"\s+content=\"([^\"]*)\">/;
    my ($title) =  ($1);
    $title = decode_entities($title) if defined $title;
    return $title;
}

sub get_youtube_desc {
    my $page = shift;

    $page =~ /<meta\s+name=\"description\"\s+content=\"([^\"]*)\">/;
    my ($desc) =  ($1);
    $desc = decode_entities($desc) if defined $desc;
    return $desc;
}

sub get_youtube_keywords {
    my $page = shift;

    $page =~ /<meta\s+name=\"keywords\"\s+content=\"([^\"]*)\">/;
    my @keywords = (split /,\s+/, $1);
    return \@keywords;
}

sub get_youtube_duration {
    my $page = shift;

    $page =~ /<meta\s+itemprop=\"duration\"\s+content=\"([^\"]*)\">/;
    my ($funky) = ($1);
    $funky =~ /.*?(\d+)M(\d+)S/;
    my ($minutes, $seconds) = ($1, $2);
    return sprintf "%d:%02d", $minutes, $seconds;
}

sub get_page_title {
    my $page = shift;

    $page =~ /<title>\s+([^\<]*?)<\/title>/;
    my ($funky) = ($1);
    return $funky;
}

my $url = shift;
my $given_uri = URI->new($url);
my $given_host = $given_uri->host;

my $channel = shift;
my ($origin, $page) = get_url($url);
#print STDERR "DEBUG: $page\n";

my $youtube_id = get_youtube_id($page) if defined $page;
my $youtube_title = get_youtube_title($page) if defined $youtube_id;
my $youtube_duration = get_youtube_duration($page) if defined $youtube_id;
my $chan_color = channel_color($channel) if defined $channel;
my $page_title = get_page_title($page) if defined $page;

#my $youtube_desc = get_youtube_desc($page);
#my $youtube_keywords = get_youtube_keywords($page);

$channel = " from $chan_color<$channel>%^RESET%^" if defined $channel and defined $chan_color;
$channel = " from <$channel>" if defined $channel and !defined $chan_color;
$channel = "" if !defined $channel;

$youtube_id = "%^YELLOW%^[$youtube_id]%^RESET%^" if defined $youtube_id;
$youtube_duration = " %^RED%^($youtube_duration)%^RESET%^" if defined $youtube_duration;

if (defined $youtube_id and defined $youtube_title and defined $youtube_duration) {
    print "YouTube $youtube_id$channel is $youtube_title$youtube_duration\n";
} elsif (defined $youtube_id and defined $youtube_title) {
    print "YouTube $youtube_id$channel is $youtube_title\n";
} elsif (defined $origin) {
    if (defined $page_title) {
        print $given_host . " URL$channel is %^YELLOW%^$page_title%^RESET%^ from " . $origin->host . "\n";
    } else {
        #print STDERR "DEBUG: " . Dumper($origin) . "\n";
        print $given_host . " URL$channel goes to " . $origin->host . "\n";
    }
}

