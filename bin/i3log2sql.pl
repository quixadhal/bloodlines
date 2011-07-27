#!/usr/bin/perl -w

use strict;
use English;
use Data::Dumper;

use Time::HiRes qw(sleep time alarm);
use Date::Parse;
use HTML::Entities;
use DBI;

my $TEXT_FILE = '/home/bloodlines/lib/secure/log/allchan.log';
my $ARCHIVE = '/home/bloodlines/lib/secure/log/archive/allchan.log-*';
my $CHATTER = '/home/bloodlines/lib/secure/save/chat.o';

my $LOGDIR = '/home/bloodlines/lib/log/chan';
my $LOCAL_MUD = 'Bloodlines';
my $network = 'i3';
my $dbc = DBI->connect('DBI:Pg:dbname=i3logs;host=localhost;port=5432;sslmode=prefer', 'quixadhal', 'tardis69', { AutoCommit => 0, PrintError => 0, });

=head1 SQL

CREATE TABLE chanlogs (
    msg_date    TIMESTAMP WITHOUT TIME ZONE DEFAULT now() NOT NULL,
    network     TEXT NOT NULL,
    channel     TEXT NOT NULL,
    speaker     TEXT NOT NULL,
    mud         TEXT NOT NULL,
    is_emote    BOOLEAN DEFAULT false,
    message     TEXT
);

CREATE INDEX ix_msg_date ON chanlogs (msg_date);
CREATE INDEX ix_channel ON chanlogs (channel);
CREATE INDEX ix_speaker ON chanlogs (speaker);
CREATE INDEX ix_mud ON chanlogs (mud);
CREATE UNIQUE INDEX ix_chanlogs ON chanlogs (msg_date, network, channel, speaker, mud, is_emote, message); 

=cut

my $add_entry_sql = $dbc->prepare( qq!
    INSERT INTO chanlogs (msg_date, network, channel, speaker, mud, message)
    VALUES (?,trim(?),trim(?),trim(?),trim(?),trim(?))
    !);

sub most_recent_sql {
    my $res = $dbc->selectrow_hashref(qq!

        SELECT *
          FROM chanlogs
      ORDER BY msg_date DESC
         LIMIT 1

    !, undef);
    print STDERR $DBI::errstr."\n" if !defined $res;
    return $res;
}

sub parse_log_line {
    my $line = shift;
    return undef if !defined $line;
    my @parts = split /\t/, $line;
    return undef if scalar(@parts) != 4;

    my %log_entry = ();

    my $timestamp = substr($parts[0], 11, 8);
    substr($timestamp, 2, 1) = ':';
    substr($timestamp, 5, 1) = ':';
    my $datestamp = substr($parts[0], 0, 10);
    substr($datestamp, 4, 1) = '-';
    substr($datestamp, 7, 1) = '-';

    $log_entry{'msg_date'} = "$datestamp $timestamp";       # Timestamp YYYY-MM-DD HH:MM:SS
    $log_entry{'network'} = $network;                       # Network is always i3

    my $channel = $parts[1];                                # Channel
    $log_entry{'channel'} = $channel;

    my $speaker = $parts[2];
    my @bits = split /@/, $speaker;
    my $name = lcfirst $bits[0];
    my $mudname = join('@', @bits[1 .. scalar(@bits)-1]);

    $log_entry{'speaker'} = $name;                          # Character
    $log_entry{'mud'} = $mudname;                           # Mud

    my $message = $parts[3];
    $log_entry{'message'} = $message;                       # Message body

    $log_entry{'is_emote'} = undef;                         # Can't tell from the logs without more parsing...

    #$message = encode_entities($message);
    #$message = s/((?:http|https|ftp)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(?::[a-zA-Z0-9]*)?\/?(?:[a-zA-Z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*)/<a href="$1" target="I3-link">$1<\/a>/;

    return \%log_entry;
}

sub load_logs {
    my $recent = most_recent_sql();
    my $recent_date = str2time($recent->{'msg_date'});
    my $oldest_date = undef;
    my $is_old = 0;

    my @files = ( $TEXT_FILE, reverse sort glob $ARCHIVE );
    my @lines = ();

    foreach my $file ( @files ) {
        open FH, '<', $file or die "Cannot open log $file: $!";
        while(my $line = <FH>) {
            chomp $line;
            my @parts = split /\t/, $line;
            if( scalar(@parts) == 4) {
                push @lines, $line;
                my $oldest = parse_log_line($line);
                $oldest_date = str2time($oldest->{'msg_date'});
                $is_old = 1 if $oldest_date < $recent_date;
            }
            #last if $is_old;
        }
        close FH;
        #@lines = grep /((?:http|https|ftp)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(?::[a-zA-Z0-9]*)?\/?(?:[a-zA-Z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*)+/, @lines;
        # $lines[0] should be the oldest log entry for the given file, so if it's older than the newest sql entry, we need go no further back.
        #my $oldest = parse_log_line($lines[0]);
        #my $oldest_date = str2time($oldest->{'msg_date'});
        print "$file : $oldest_date - $recent_date\n";
        print "$file is OLDER than SQL\n" if $is_old;
        #print "$file is NEWER than SQL\n" if $oldest_date >= $recent_date;
        last if $is_old;
    }
    @lines = sort @lines;
    my $total = scalar @lines;
    print "Collected $total lines to insert\n";
    my $done = 0;
    foreach my $line (@lines) {
        my $entry = parse_log_line($line);
        my $entry_date = str2time($entry->{'msg_date'});
        $done += add_entry($entry) if defined $entry and $entry_date >= $recent_date;
    }
    print "Inserted $done lines\n";
}

sub add_entry {
    my $data = shift;

    my $rv = $add_entry_sql->execute($data->{'msg_date'}, $data->{'network'}, $data->{'channel'}, $data->{'speaker'}, $data->{'mud'}, $data->{'message'});
    if($rv) {
        $dbc->commit;
        return 1;
    } else {
        print STDERR $DBI::errstr."\n";
        $dbc->rollback;
        return 0;
    }
}

load_logs();

$dbc->disconnect();
exit 1;
