#!/usr/bin/perl -w

use strict;
use Text::Metaphone;

my @words = @ARGV;

foreach my $word (@words) {
    print "$word -> ";
    $word = uc $word;

    my $meta = Metaphone($word);

    $word =~ s/([^C])\1/$1/g;               # Drop duplicate adjacent letters, except for C.

    $word =~ s/^[KGP](?=N)//;               # If the word begins with 'KN', 'GN', 'PN', 'AE', 'WR', drop the first letter.
    $word =~ s/^A(?=E)//;
    $word =~ s/^W(?=R)//;

    $word =~ s/(?<=M)B//g;                  # Drop 'B' if after 'M' and if it is at the end of the word.
    $word =~ s/B$//;

    $word =~ s/C(?=IA)/X/g;                 # 'C' transforms to 'X' if followed by 'IA' or 'H'
    $word =~ s/(?<!S)C(?=H)/X/g;            # (unless in latter case, it is part of '-SCH-', in which case it transforms to 'K').
    $word =~ s/(?<=S)C(?=H)/K/g;
    $word =~ s/C(?=[IEY])/S/g;              # 'C' transforms to 'S' if followed by 'I', 'E', or 'Y'.
    $word =~ s/C/K/g;                       # Otherwise, 'C' transforms to 'K'.

    $word =~ s/D(?=G[EYI])/J/g;             # 'D' transforms to 'J' if followed by 'GE', 'GY', or 'GI'.
    $word =~ s/D/T/g;                       # Otherwise, 'D' transforms to 'T'.

    $word =~ s/G(?=H[^AEIOU])//g;           # Drop 'G' if followed by 'H' and 'H' is not at the end or before a vowel.
    $word =~ s/G(?=N(ED)?$)//;              # Drop 'G' if followed by 'N' or 'NED' and is at the end.

    $word =~ s/(?<!G)G(?=[IEY])/J/g;        # 'G' transforms to 'J' if before 'I', 'E', or 'Y', and it is not in 'GG'.
    $word =~ s/G/K/g;                       # Otherwise, 'G' transforms to 'K'.

    $word =~ s/(?<=[AEIOU])H(?![AEIOU])//g; # Drop 'H' if after vowel and not before a vowel.

    $word =~ s/CK/K/g;                      # 'CK' transforms to 'K'.

    $word =~ s/PH/F/g;                      # 'PH' transforms to 'F'.

    $word =~ s/Q/K/g;                       # 'Q' transforms to 'K'.

    $word =~ s/S(?=(H|IO|IA))/X/g;          # 'S' transforms to 'X' if followed by 'H', 'IO', or 'IA'.

    $word =~ s/T(?=(IA|IO))/X/g;            # 'T' transforms to 'X' if followed by 'IA' or 'IO'.
    $word =~ s/TH/0/g;                      # 'TH' transforms to '0'.
    $word =~ s/T(?=CH)//g;                  # Drop 'T' if followed by 'CH'.

    $word =~ s/V/F/g;                       # 'V' transforms to 'F'.

    $word =~ s/^WH/W/;                      # 'WH' transforms to 'W' if at the beginning.
    $word =~ s/W(?![AEIOU])//g;             # Drop 'W' if not followed by a vowel.

    $word =~ s/^X/S/;                       # 'X' transforms to 'S' if at the beginning.
    $word =~ s/X/KS/g;                      # Otherwise, 'X' transforms to 'KS'.

    $word =~ s/Y(?![AEIOU])//g;             # Drop 'Y' if not followed by a vowel.

    $word =~ s/Z/S/g;                       # 'Z' transforms to 'S'.

    $word =~ s/(?<=.)[AEIOU]//g;            # Drop all vowels unless it is the beginning.

    print "Text::Metaphone == $meta, Local == $word\n";
}
