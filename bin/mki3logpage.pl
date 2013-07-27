#!/usr/bin/perl -w

use strict;
use English;
use Data::Dumper;

use Time::HiRes qw(sleep time alarm);
use Date::Parse;
use POSIX qw(strftime);
use Lingua::EN::Numbers::Ordinate;
use HTML::Entities;
use DBI;

my $dbc = DBI->connect('DBI:Pg:dbname=i3logs;host=localhost;port=5432;sslmode=prefer', 'bloodlines', 'tardis69', { AutoCommit => 0, PrintError => 0, });

sub get_quote {
    my $res = $dbc->selectrow_hashref(qq!

        SELECT *
         FROM chanlogs
        WHERE msg_date >= now() - '1 day'::interval
          AND is_url IS NOT TRUE
          AND is_emote IS NOT TRUE
          AND is_bot IS NOT TRUE
          AND channel IN ('intergossip', 'dchat', 'intercre')
       OFFSET random() * (
            SELECT COUNT(*)
             FROM chanlogs
            WHERE msg_date >= now() - '1 day'::interval
              AND is_url IS NOT TRUE
              AND is_emote IS NOT TRUE
              AND is_bot IS NOT TRUE
              AND channel IN ('intergossip', 'dchat', 'intercre')
          )
       LIMIT  1

        !, undef);
    print STDERR $DBI::errstr."\n" if !defined $res;
    return $res;
}

sub fetch_word_count {
    my $timeframe = shift || '7 days';
    my $counttype = shift || 'fn_wordcount';
    # Valid values are fn_wordcount, fn_properwordcount, or length

    my $res = $dbc->selectall_arrayref(qq!

 SELECT foo.speaker, sum(foo.wordcount) AS words
   FROM ( SELECT chanlogs.speaker, $counttype(chanlogs.message) AS wordcount
           FROM chanlogs
          WHERE NOT chanlogs.is_bot AND chanlogs.msg_date >= (now() - '$timeframe'::interval)
          GROUP BY chanlogs.speaker, chanlogs.message) foo
  GROUP BY foo.speaker
  ORDER BY sum(foo.wordcount) DESC
  LIMIT 10

    !, { Slice => {} } );
    print STDERR $DBI::errstr."\n" if !defined $res;
    return $res;
}

sub pie_chart {
    my $timeframe = shift || '1 day';
    my $counttype = shift || 'fn_wordcount';
    my $side = shift || 'left';
    my $funcname = shift || 'drawDaily';
    my $caption = shift || 'Stuff people said yesterday';
    my $funcname_div = $funcname . "_div";

    my $page = <<EOM
      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback($funcname);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function $funcname() {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Speaker');
        data.addColumn('number', 'Words');
        data.addRows([
EOM
;
    my @row_data = @{ fetch_word_count( $timeframe, $counttype ) };
    my $outputrows = [];
    foreach my $row (@row_data) {
        push @$outputrows, ( "['" . $row->{'speaker'} . "', " . $row->{'words'} . "]" );
    }
    $page .= join ",\n", @$outputrows;
    $page .= "\n";
    $page .= <<EOM
        ]);

        // Set chart options
        var options = {
                       "title":'$caption',
                       "legend":'$side',
                       // "backgroundColor": { fill: "none" },
                       // "backgroundColor": "#505050",
                       "width":400,
                       "height":300,
                       "is3D":'true'
                      };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('$funcname_div'));
        chart.draw(data, options);
      }
EOM
;
    return $page;
}

my $daily_chunk = pie_chart('1 day', 'fn_wordcount', 'left', 'drawDaily', 'Stuff people said yesterday');
my $weekly_chunk = pie_chart('1 week', 'fn_wordcount', 'right', 'drawWeekly', 'Stuff people said this week');
my $monthly_chunk = pie_chart('1 month', 'fn_wordcount', 'left', 'drawMonthly', 'Stuff people said this month');
my $yearly_chunk = pie_chart('1 year', 'fn_wordcount', 'right', 'drawYearly', 'Stuff people said this year');
my $quote = get_quote();
my $quote_name = $quote->{'speaker'} . '@' . $quote->{'mud'};
my $quote_text = $quote->{'message'};
$quote_name =~ s/\s+/&nbsp;/gmix;
my $now_time = time();
my $hour_num = 0 + POSIX::strftime("%l", localtime($now_time));
my $min_num = 0 + POSIX::strftime("%M", localtime($now_time));
my $month_num = 0 + POSIX::strftime("%e", localtime($now_time));
$hour_num++ if $min_num > 40;

my $part = "$hour_num o'clock";
$part = "quarter past $hour_num" if $min_num > 10;
$part = "half past $hour_num" if $min_num > 25;
$part = "quarter to $hour_num" if $min_num > 40;
$part = "$hour_num o'clock" if $min_num > 55;
my $month_part = ordinate($month_num);

my $current_time = POSIX::strftime("It was recently $part, on %A, the $month_part of %B, %Y.", localtime($now_time));

my $page = <<EOM
<html>
  <head>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

      $daily_chunk

      $weekly_chunk

      $monthly_chunk

      $yearly_chunk

    </script>
  </head>

  <!-- <body background="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/dark_wood.jpg" bgcolor="#505050" text="#d0d0d0" link="#ffffbf" vlink="#ffa040"> -->
  <body>
    <table id="piecharts" border=0 cellspacing=0 cellpadding=0 width=80% align="center">
      <tr>
        <td align="center" valign="bottom" colspan="2">
          <h3>$current_time</h3>
        </td>
      </tr>
      <tr>
        <td align="center" valign="top">
          <div id="drawDaily_div"></div>
        </td>
        <td align="center" valign="top">
          <div id="drawWeekly_div"></div>
        </td>
      </tr>
      <tr>
        <td align="center" valign="bottom">
          <div id="drawMonthly_div"></div>
        </td>
        <td align="center" valign="bottom">
          <div id="drawYearly_div"></div>
        </td>
      </tr>
      <tr>
        <td align="center" valign="center" colspan="2">
          <h3><span style="color:blue">$quote_name</span>&nbsp;said &quot;<i>$quote_text</i>&quot;</h3>
        </td>
      </tr>
    </table>
  </body>
</html>
EOM
;

print "$page\n";

