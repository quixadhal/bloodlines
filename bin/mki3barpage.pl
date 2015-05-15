#!/usr/bin/perl -w

use strict;
use English;
use Data::Dumper;

use Time::HiRes qw(sleep time alarm);
use Date::Parse;
use HTML::Entities;
use DBI;

my $dbc = DBI->connect('DBI:Pg:dbname=i3log2;host=localhost;port=5432;sslmode=prefer', 'bloodlines', 'tardis69', { AutoCommit => 0, PrintError => 0, });

sub get_quote {
    my $res = $dbc->selectrow_hashref(qq!

        SELECT *
         FROM chanlogs
        WHERE msg_date >= now() - '1 day'::interval
          AND is_url IS NOT TRUE
          AND is_emote IS NOT TRUE
          AND is_bot IS NOT TRUE
          AND channel IN ('intergossip', 'dchat', 'intercre', 'discworld-chat')
       OFFSET random() * (
            SELECT COUNT(*)
             FROM chanlogs
            WHERE msg_date >= now() - '1 day'::interval
              AND is_url IS NOT TRUE
              AND is_emote IS NOT TRUE
              AND is_bot IS NOT TRUE
              AND channel IN ('intergossip', 'dchat', 'intercre', 'discworld-chat')
          )
       LIMIT  1

        !, undef);
    print STDERR $DBI::errstr."\n" if !defined $res;
    return $res;
}

sub fetch_channel_counts {
    my $timeframe = shift || '7 days';

    my $res = $dbc->selectall_arrayref(qq!

 SELECT channel, count(message) AS chatcount
   FROM chanlogs
  WHERE is_bot IS NOT TRUE AND msg_date >= (now() - '$timeframe'::interval)
  GROUP BY channel
  ORDER BY chatcount DESC
  LIMIT 10

    !, { Slice => {} } );
    print STDERR $DBI::errstr."\n" if !defined $res;
    return $res;
}

sub bar_chart {
    my $timeframe = shift || '1 day';
    my $side = shift || 'left';
    my $funcname = shift || 'drawDaily';
    my $caption = shift || 'Channels people talked on yesterday';
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
        data.addColumn('string', 'Channel');
        data.addColumn('number', 'Messages');
        data.addRows([
EOM
;
    my @row_data = @{ fetch_channel_counts( $timeframe ) };
    my $outputrows = [];
    foreach my $row (@row_data) {
        push @$outputrows, ( "['" . $row->{'channel'} . "', " . $row->{'chatcount'} . "]" );
    }
    $page .= join ",\n", @$outputrows;
    $page .= "\n";
    $page .= <<EOM
        ]);

        // Set chart options
        var options = {
                       "title":'$caption',
                       "legend":'none',
                       // "backgroundColor": { fill: "none" },
                       // "backgroundColor": "#505050",
                       "width":400,
                       "height":300,
                       "is3D":'true'
                      };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.BarChart(document.getElementById('$funcname_div'));
        chart.draw(data, options);
      }
EOM
;
    return $page;
}

sub new_bar_chart {
    my $timeframe = shift || '1 day';
    my $side = shift || 'left';
    my $funcname = shift || 'drawDaily';
    my $caption = shift || 'Channels people talked on yesterday';
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
        // data.addColumn('string', 'Channel');
        // data.addColumn('number', 'Messages');
        // data.addRows([
EOM
;
    my @row_data = @{ fetch_channel_counts( $timeframe ) };
    foreach my $row (@row_data) {
        $page .= "data.addColumn('number', '" . $row->{'channel'} . "');\n";
    }
    $page .= "data.addRows(1);\n";
    my $c = 0;
    foreach my $row (@row_data) {
        $page .= "data.setValue(0,$c," . $row->{'chatcount'} . ");\n";
        $c++;
    }
    $page .= "\n";
    $page .= <<EOM
        // Set chart options
        var options = {
                       "title":'$caption',
                       "legend":'none',
                       // "backgroundColor": { fill: "none" },
                       // "backgroundColor": "#505050",
                       "width":400,
                       "height":300,
                       "is3D":'true'
                      };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.BarChart(document.getElementById('$funcname_div'));
        chart.draw(data, options);
      }
EOM
;
    return $page;
}

my $daily_chunk = bar_chart('1 day', 'left', 'drawDaily', 'Stuff people said yesterday');
my $weekly_chunk = bar_chart('1 week', 'right', 'drawWeekly', 'Stuff people said this week');
my $monthly_chunk = bar_chart('1 month', 'left', 'drawMonthly', 'Stuff people said this month');
my $yearly_chunk = bar_chart('1 year', 'right', 'drawYearly', 'Stuff people said this year');
my $quote = get_quote();
my $quote_name = $quote->{'speaker'} . '@' . $quote->{'mud'};
my $quote_text = $quote->{'message'};
$quote_name =~ s/\s+/&nbsp;/gmix;

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

