#!/usr/bin/perl -w

use strict;
use English;
use Data::Dumper;

use Time::HiRes qw(sleep time alarm);
use Date::Parse;
use HTML::Entities;
use DBI;
use Encode;
use Parallel::ForkManager 0.7.6;
use LWP::UserAgent;

my $dbc = DBI->connect('DBI:Pg:dbname=i3log2;host=localhost;port=5432;sslmode=prefer', 'bloodlines', 'tardis69', { AutoCommit => 0, PrintError => 0, });
my $sth_update = $dbc->prepare( qq!
    UPDATE videos SET disabled = ?
    WHERE video_id = ?
!);

# video_id    | text    | not null
# video_len   | integer | not null
# description | text    |
# plays       | integer | default 0
# disabled    | boolean | default false

my $key_field = 'video_id';
my $list = $dbc->selectall_hashref(qq!
    SELECT video_id, description
    FROM videos
    WHERE NOT disabled
!, $key_field);
$dbc->disconnect();
$dbc = undef;

my $pm = Parallel::ForkManager->new(20);
foreach my $id (sort { $a cmp $b } keys %$list) {
    $pm->start() and next;
    do_verify($list->{$id});
    $pm->finish(0);
}
$pm->wait_all_children;
exit 1;

sub do_verify {
    my $data = shift;
    return if !defined $data;

    sleep (0.5 + rand() * 0.5);
    my $dbc = DBI->connect('DBI:Pg:dbname=i3log2;host=localhost;port=5432;sslmode=prefer', 'bloodlines', 'tardis69', { AutoCommit => 0, PrintError => 0, });
    my $sth_update = $dbc->prepare( qq!
        UPDATE videos SET disabled = ?
        WHERE video_id = ?
!);
    my $id = $data->{'video_id'};
    my $desc = $data->{'description'};

    my $failed = 0;
    my $url = "https://www.youtube.com/watch?v=$id";
    my $page = get_url( $url );

    $failed = 1 if !defined $page or $page =~ /This video has been removed/ or $page =~ /This video is no longer available/;
    #$failed = 1 if $page =~ /<\s*h1\s+id\s*=\s*\"unavailable-message\"\s+class\s*=\s*\"message\">/gsmix;

    if($failed) {
        print "FAILED: $id, $desc\n";
        my $rv_update = $sth_update->execute('t', $id);
        if($rv_update) {
            $dbc->commit;
        } else {
            $dbc->rollback;
        }
    } else {
        print "OK: $id, $desc\n";
    }

    $dbc->disconnect();
    $dbc = undef;
}

sub get_url {
  my $url = shift;

  return undef if !defined $url;
  my $timeout = 90;
  my $lwp = LWP::UserAgent->new();
     $lwp->timeout($timeout/2);
  my $request = HTTP::Request->new(GET => $url);
  my $response = undef;
  #print "Fetching... $url\n";
  if($request) {
    eval {
      local $SIG{ALRM} = sub { die "Exceeded Timeout of $timeout for $url\n" };
      alarm $timeout;
      $response = $lwp->request($request);
      alarm 0;
    };
    warn "Timeout" if($EVAL_ERROR and ($EVAL_ERROR =~ /^Exceeded Timeout/));
    return $response->content if((defined $response) and $response->is_success);
  }
  return undef;
}
