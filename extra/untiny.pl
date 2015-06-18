#!/usr/bin/perl -w
# Created by Quixadhal, 2010.05.01

# Fed a URL, this script will fetch it and return information about the actual
# destination URL and, in the case of youtube, the video in question.

# <link rel="canonical" href="/watch?v=IleiqUDYpFQ">
# <link rel="alternate" media="handheld" href="http://m.youtube.com/watch?desktop_uri=%2Fwatch%3Fv%3DIleiqUDYpFQ&amp;v=IleiqUDYpFQ&amp;gl=US">
# <meta name="title" content="Ronald Reagan: First Inaugural Address (1 of 3)">
# <meta name="description" content="Senator Hatfield, Mr. Chief Justice, Mr. President, Vice President Bush, Vice President Mondale, Senator Baker, Speaker O\'Neill, Reverend Moomaw, and my fell...">
# <meta name="keywords" content="president, ronald, reagan, first, inaugural, inauguration, address, washington, 80s, reaganomics, ronny, gipper, carter, jfk, kennedy, bush, clinton, republi...">

# Here are the triggers I used in Tinyfugue, to feed it.
#
#; 00:45 <intergossip> Kalinash@Fire and Ice: http://www.youtube.com/watch?v=IleiqUDYpFQ
#
#/def -mregexp -p2 -t"<(\w+)>.*(http://tinyurl.com/[^&\?\.]+)" check_tiny_chan = /quote -0 %P1 !~/bin/untiny.pl '%P2'
#/def -mregexp -p2 -t"<(\w+)>.*(http://bit.ly/[^&\?\.]+)" check_bitly_chan = /quote -0 %P1 !~/bin/untiny.pl '%P2'
#/def -mregexp -p2 -t"<(\w+)>.*(http://goo.gl/[^&\?\.]+)" check_googl_chan = /quote -0 %P1 !~/bin/untiny.pl '%P2'
#/def -mregexp -p2 -t"<(\w+)>.*(http://www.youtube.com/watch\?v=[^&\?\.]+)" check_youtube_chan = /if (%P1 !~ "ichat") /quote -0 %P1 !~/bin/untiny.pl '%P2'%; /endif

#/def -mregexp -p2 -t"\[(\w+)\].*(http://tinyurl.com/[^&\?\.]+)" check_tiny_chan = /if (%P1 =~ "imud_gossip") /quote -0 %P1 !~/bin/untiny.pl '%P2'%; /endif
#/def -mregexp -p2 -t"\[(\w+)\].*(http://bit.ly/[^&\?\.]+)" check_bitly_chan = /if (%P1 =~ "imud_gossip") /quote -0 %P1 !~/bin/untiny.pl '%P2'%; /endif
#/def -mregexp -p2 -t"\[(\w+)\].*(http://goo.gl/[^&\?\.]+)" check_googl_chan = /if (%P1 =~ "imud_gossip") /quote -0 %P1 !~/bin/untiny.pl '%P2'%; /endif
#/def -mregexp -p2 -t"\[(\w+)\].*(http://www.youtube.com/watch\?v=[^&\?\.]+)" check_youtube_chan = /if (%P1 =~ "imud_gossip") /quote -0 %P1 !~/bin/untiny.pl '%P2'%; /endif
#
# You can force posting on a particular channel by replacing the %P1 after /quote with a fixed channel string.
#
#/def -mregexp -p2 -t"<(\w+)>.*(https?://tinyurl.com/[^&\?\.]+)" check_tiny_chan = /quote -0 url !~/bin/untiny.pl '%P2'
#/def -mregexp -p2 -t"<(\w+)>.*(https?://bit.ly/[^&\?\.]+)" check_bitly_chan = /quote -0 url !~/bin/untiny.pl '%P2'
#/def -mregexp -p2 -t"<(\w+)>.*(https?://goo.gl/[^&\?\.]+)" check_googl_chan = /quote -0 url !~/bin/untiny.pl '%P2'
#/def -mregexp -p2 -t"<(\w+)>.*(https?://www.youtube.com/watch\?v=[^&\?\.]+)" check_youtube_chan = /if (%P1 !~ "url") /quote -0 url !~/bin/untiny.pl '%P2'%; /endif


use strict;
use English;
use Data::Dumper;
use HTTP::Request::Common qw(POST);
use LWP::UserAgent;
use URI;

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

    #print "DEBUG: given URL:  $given_uri\n";
    #print "DEBUG: given HOST: $given_host\n";

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
                #print "DEBUG: $origin\n";
                #print "DEBUG: " . Dumper($prev) . "\n";
                last if defined $origin;
            }
            #print "DEBUG: origin URL: $origin\n" if defined $origin;
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
    return $title;
}

sub get_youtube_desc {
    my $page = shift;

    $page =~ /<meta\s+name=\"description\"\s+content=\"([^\"]*)\">/;
    my ($desc) =  ($1);
    return $desc;
}

sub get_youtube_keywords {
    my $page = shift;

    $page =~ /<meta\s+name=\"keywords\"\s+content=\"([^\"]*)\">/;
    my @keywords = (split /,\s+/, $1);
    return \@keywords;
}

my $url = shift;
my $given_uri = URI->new($url);
my $given_host = $given_uri->host;

my $channel = shift;
$channel = " from ($channel)" if defined $channel;
$channel = "" if !defined $channel;
my ($origin, $page) = get_url($url);
#print "DEBUG: $page\n";

my $youtube_id = get_youtube_id($page) if defined $page;
my $youtube_title = get_youtube_title($page) if defined $youtube_id;
#my $youtube_desc = get_youtube_desc($page);
#my $youtube_keywords = get_youtube_keywords($page);

if (defined $youtube_id and defined $youtube_title) {
    print "YouTube [$youtube_id]$channel is $youtube_title\n";
} elsif (defined $origin) {
    #print "DEBUG: " . Dumper($origin) . "\n";
    print $given_host . " URL$channel goes to " . $origin->host . "\n";
}

