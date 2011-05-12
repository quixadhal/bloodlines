#!/usr/bin/perl -w

# This overly complicated script creates the pinkfish.h header file
# which I've used to contain all the various Pinkfish codes used
# throughout the Dead Souls codebase.
#
# It supports extended Pinkfish, and by that I mean 16 color ANSI
# as well as xterm-256.
#
# Enjoy!
#
# Dread Quixadhal - quixadhal@shadowlord.org

#MXP uses: %^#RRGGBB%^ and %^B_#RRGGBB%^

# Template for output:
# term_info["xterm256"] = ([
#   "NAME" : ANSI(x), ESC(x), etc...
# ]);

use Data::Dumper;

my $FORMAT_FIRST = 0;
my $FORMAT_NONE = 0;
my $FORMAT_ANSI = 1;
my $FORMAT_XTERM = 2;
my $FORMAT_GREY = 3;
#my $FORMAT_LAST = 3;
my $FORMAT_IMC2 = 4;
my $FORMAT_I3 = 5;
my $FORMAT_MXP = 6;
my $FORMAT_LAST = 6;
#my $FORMAT_HTML = 6;
#my $FORMAT_LAST = 6;

#my @term_name = ( "unknown", "ansi", "xterm-256color", "xterm-grey", "imc2", "i3", "mxp", "html" );
my @term_name = ( "unknown", "ansi", "xterm-256color", "xterm-grey", "imc2", "i3", "mxp" );

my $RESET = "\033[0m";
my $BOLD = "\033[1m";
my $ITALIC = "\033[3m";
my $UNDERLINE = "\033[4m";
my $FLASH = "\033[5m";
my $REVERSE = "\033[7m";
my $STRIKETHRU = "\033[9m";

my $CURS_UP = "\033[A";
my $CURS_DOWN = "\033[B";
my $CURS_RIGHT = "\033[C";
my $CURS_LEFT = "\033[D";

my $HOME = "\033[H";
my $SAVE = "\033[s";
my $RESTORE = "\033[u";
#my $STATUS = "\033[23;24r\0338";
#my $WINDOW = "\0337\033[0;22r";
#my $INITTERM = "\033[H\033[J\033[23;24r\03323H";
#my $ENDTERM = "\033[0r\033[H\033[J";
#my $CLEARLINE = "\r";
my $INITTERM = "\033[H\033[2J";
my $ENDTERM = "";
my $CLEARLINE = "\033[L\033[G";
my $STATUS = "";
my $WINDOW = "";

my %attr_trans;
my %terminal_trans;
#my %html_trans;
my %ansi_trans;
my %xterm_trans;
my %x11_trans;

my @hexes;
my @greys;
my @ansi;
my @old_pinkfish;
my @old_b_pinkfish;
my @ansi_name;
my @ansi_b_name;
my @xterm_name;
my @xterm_b_name;
my @x11_name;
my @x11_b_name;
my %x11_values;
my %imc_pinkfish;
my %pinkfish_imc;
my %pinkfish_imc_cleanup;
my %pinkfish_imc_ext;
my @imc_tokens;
my @imc_back_tokens;

my %rgb_xterm;
my %xterm_rgb;
my %rgb_x11;
my %x11_rgb;

sub hex2int { return hex(shift); }
sub int2hex { return sprintf("%02x", shift); }
sub strpad { my ($s,$w) = (shift,shift); return sprintf("%*s", $w, $s); }
sub zeropad { my ($s,$w) = (shift,shift); return sprintf("%0*s", $w, $s); }
sub int2base6 { 
    my $color = shift;
    $color -= 16;
    return "000" if $color < 0;
    return "555" if $color > 255;
    return sprintf("%d%d%d", ($color/36)%6, ($color/6)%6, $color%6);
}
sub base62int {
    my $str = shift;
    return 0 if ! $str =~ /\d\d\d/;
    $str[0] = '5' if $str[0] > '5';
    $str[1] = '5' if $str[1] > '5';
    $str[2] = '5' if $str[2] > '5';
    return ((0+$str[0])*36) + ((0+$str[1])*6) + (0+$str[2]) + 16;
}

sub setup_hex_arrays {
    my $i;
    my ($colour, $red, $green, $blue);

    %rgb_xterm = ();
    %xterm_rgb = ();
    @hexes = ( 0x00, 0x55, 0x88, 0xBB, 0xDD, 0xFF );
    @greys = ( 0x08, 0x12, 0x1c, 0x26, 0x30, 0x3a,
               0x44, 0x4e, 0x58, 0x62, 0x6c, 0x76,
               0x80, 0x8a, 0x94, 0x9e, 0xa8, 0xb2,
               0xbc, 0xc6, 0xd0, 0xda, 0xe4, 0xee );
    @ansi = (
            [ 0x00, 0x00, 0x00 ], # 0  black
            [ 0xBB, 0x00, 0x00 ], # 1  red
            [ 0x00, 0xBB, 0x00 ], # 2  green
            [ 0xBB, 0xBB, 0x00 ], # 3  orange
            [ 0x00, 0x00, 0xBB ], # 4  blue
            [ 0xBB, 0x00, 0xBB ], # 5  magenta
            [ 0x00, 0xBB, 0xBB ], # 6  cyan
            [ 0xBB, 0xBB, 0xBB ], # 7  light grey

            [ 0x55, 0x55, 0x55 ], # 8  dark grey
            [ 0xFF, 0x55, 0x55 ], # 9  bright red
            [ 0x55, 0xFF, 0x55 ], # 10 bright green
            [ 0xFF, 0xFF, 0x55 ], # 11 yellow
            [ 0x55, 0x55, 0xFF ], # 12 bright blue
            [ 0xFF, 0x55, 0xFF ], # 13 bright magenta
            [ 0x55, 0xFF, 0xFF ], # 14 bright cyan
            [ 0xFF, 0xFF, 0xFF ]  # 15 white
            );
    @old_pinkfish = (
                 '%^BLACK%^', '%^RED%^', '%^GREEN%^', '%^ORANGE%^',
                 '%^BLUE%^', '%^MAGENTA%^', '%^CYAN%^', '%^WHITE%^',
                 '%^BOLD%^%^BLACK%^', '%^BOLD%^%^RED%^', '%^BOLD%^%^GREEN%^', '%^YELLOW%^',
                 '%^BOLD%^%^BLUE%^', '%^BOLD%^%^MAGENTA%^', '%^BOLD%^%^CYAN%^', '%^BOLD%^%^WHITE%^'
                 );
    @old_b_pinkfish = (
                 '%^B_BLACK%^', '%^B_RED%^', '%^B_GREEN%^', '%^B_ORANGE%^',
                 '%^B_BLUE%^', '%^B_MAGENTA%^', '%^B_CYAN%^', '%^B_WHITE%^',
                 '%^B_BLACK%^', '%^B_RED%^', '%^B_GREEN%^', '%^B_YELLOW%^',
                 '%^B_BLUE%^', '%^B_MAGENTA%^', '%^B_CYAN%^', '%^B_WHITE%^'
                 );
    @ansi_name = (
                 'BLACK', 'RED', 'GREEN', 'ORANGE',
                 'BLUE', 'MAGENTA', 'CYAN', 'GREY',
                 'DARKGREY', 'LIGHTRED', 'LIGHTGREEN', 'YELLOW',
                 'LIGHTBLUE', 'PINK', 'LIGHTCYAN', 'WHITE'
                 );
    push @ansi_b_name, "B_$_" foreach (@ansi_name);

    @imc_tokens = (
                 '~x', '~r', '~g', '~y',
                 '~b', '~p', '~c', '~w',
                 '~z', '~R', '~G', '~Y',
                 '~B', '~P', '~C', '~W'
                 );
    @imc_back_tokens = (
                 '^x', '^r', '^g', '^O',
                 '^b', '^p', '^c', '^w',
                 '^z', '^R', '^G', '^Y',
                 '^B', '^P', '^C', '^W'
                 );

    for( my $i = 0; $i < 16; $i++ ) {
        $red = $ansi[$i][0];
        $green = $ansi[$i][1];
        $blue = $ansi[$i][2];
        $rgb_xterm{ $red * 255 * 255 + $green * 255 + $blue } = $i;
        $xterm_rgb{$i} = [ $red, $green, $blue ];
    }
    for( my $i = 16; $i < 232; $i++ ) {
        $colour = $i - 16;
        $red = $hexes[($colour/36)%6];
        $green = $hexes[($colour/6)%6];
        $blue = $hexes[($colour)%6];
        $rgb_xterm{ $red * 255 * 255 + $green * 255 + $blue } = $i;
        $xterm_rgb{$i} = [ $red, $green, $blue ];
    }
    for( $i = 232; $i < 256; $i++ ) {
        $colour = $i - 232;
        $red = $greys[$colour%24];
        $green = $red;
        $blue = $red;
        $rgb_xterm{ $red * 255 * 255 + $green * 255 + $blue } = $i;
        $xterm_rgb{$i} = [ $red, $green, $blue ];
    }
}

sub setup_colour_maps
{
  $attr_trans{$FORMAT_NONE} = {
                   'RESET'      => '',
                   'BOLD'       => '',
                   'ITALIC'     => '',
                   'UNDERLINE'  => '',
                   'FLASH'      => '',
                   'REVERSE'    => '',
                   'STRIKETHRU' => '',
                  };
  $attr_trans{$FORMAT_ANSI} = {
                   'RESET'      => $RESET,
                   'BOLD'       => $BOLD,
                   'ITALIC'     => $ITALIC,
                   'UNDERLINE'  => $UNDERLINE,
                   'FLASH'      => $FLASH,
                   'REVERSE'    => $REVERSE,
                   'STRIKETHRU' => $STRIKETHRU,
               };
  $attr_trans{$FORMAT_XTERM} = $attr_trans{$FORMAT_ANSI};
  $attr_trans{$FORMAT_GREY} = $attr_trans{$FORMAT_ANSI};
  $attr_trans{$FORMAT_IMC2} = {
                   'RESET'      => '~!',
                   'BOLD'       => '~L',
                   'ITALIC'     => '~i',
                   'UNDERLINE'  => '~u',
                   'FLASH'      => '~$',
                   'REVERSE'    => '~v',
                   'STRIKETHRU' => '~s',
  };
  $attr_trans{$FORMAT_I3} = {
                   'RESET'      => '%^RESET%^',
                   'BOLD'       => '%^BOLD%^',
                   'ITALIC'     => '%^ITALIC%^',
                   'UNDERLINE'  => '%^UNDERLINE%^',
                   'FLASH'      => '%^FLASH%^',
                   'REVERSE'    => '%^REVERSE%^',
                   'STRIKETHRU' => '%^STRIKETHRU%^',
  };
  $attr_trans{$FORMAT_MXP} = {
                   'RESET'      => '<RESET>',
                   'BOLD'       => '<BOLD>',
                   'ITALIC'     => '<ITALIC>',
                   'UNDERLINE'  => '<UNDERLINE>',
                   'FLASH'      => '<FONT COLOR=BLINK>',
                   'REVERSE'    => '<FONT COLOR=INVERSE>',
                   'STRIKETHRU' => '<STRIKEOUT>',
  };

#  $attr_trans{$FORMAT_HTML} = {
#                   'RESET'      => '</SPAN>',
#                   'BOLD'       => '<SPAN style=\'text-decoration: bold;\'>',
#                   'ITALIC'     => '<SPAN style=\'text-decoration: italic;\'>',
#                   'UNDERLINE'  => '<SPAN style=\'text-decoration: underline;\'>',
#                   'FLASH'      => '<SPAN style=\'text-decoration: blink;\'>',
#                   'REVERSE'    => '',
#                   'STRIKETHRU' => '<SPAN style=\'text-decoration: line-through;\'>',
#               };


  $terminal_trans{$FORMAT_NONE} = {
                   'CURS_UP'     => '',
                   'CURS_DOWN'   => '',
                   'CURS_RIGHT'  => '',
                   'CURS_LEFT'   => '',

                   'CLEARLINE'   => '',
                   'INITTERM'    => '',
                   'ENDTERM'     => '',
                   'SAVE'        => '',
                   'RESTORE'     => '',
                   'HOME'        => '',
               };
  $terminal_trans{$FORMAT_ANSI} = {
                   'CURS_UP'     => $CURS_UP,
                   'CURS_DOWN'   => $CURS_DOWN,
                   'CURS_RIGHT'  => $CURS_RIGHT,
                   'CURS_LEFT'   => $CURS_LEFT,

                   'CLEARLINE'   => $CLEARLINE,
                   'INITTERM'    => $INITTERM,
                   'ENDTERM'     => $ENDTERM,
                   'SAVE'        => $SAVE,
                   'RESTORE'     => $RESTORE,
                   'HOME'        => $HOME,
               };
  $terminal_trans{$FORMAT_XTERM} = $terminal_trans{$FORMAT_ANSI};
  $terminal_trans{$FORMAT_GREY} = $terminal_trans{$FORMAT_XTERM};
  $terminal_trans{$FORMAT_IMC2} = $terminal_trans{$FORMAT_NONE};
  $terminal_trans{$FORMAT_I3} = {
                   'CURS_UP'     => '%^CURS_UP%^',
                   'CURS_DOWN'   => '%^CURS_DOWN%^',
                   'CURS_RIGHT'  => '%^CURS_RIGHT%^',
                   'CURS_LEFT'   => '%^CURS_LEFT%^',

                   'CLEARLINE'   => '%^CLEARLINE%^',
                   'INITTERM'    => '%^INITTERM%^',
                   'ENDTERM'     => '%^ENDTERM%^',
                   'SAVE'        => '%^SAVE%^',
                   'RESTORE'     => '%^RESTORE%^',
                   'HOME'        => '%^HOME%^',
               };
  $terminal_trans{$FORMAT_MXP} = $terminal_trans{$FORMAT_NONE};
#  $terminal_trans{$FORMAT_HTML} = $terminal_trans{$FORMAT_NONE};

#  $html_trans{$FORMAT_NONE} = {
#                'BR'        => '',
#                'P'         => '',
#                '/P'        => '',
#                '>'         => '',
#                'HREF'      => '',
#                'NAME'      => '',
#                '/A'        => '',
#                'I'         => '',
#                '/I'        => '',
#                'PRE'       => '',
#                '/PRE'      => '',
#                'STRONG'    => '',
#                '/STRONG'   => '',
#                'TABLE'     => '',
#                '/TABLE'    => '',
#                'TR'        => '',
#                '/TR'       => '',
#                'TD'        => '',
#                '/TD'       => '',
#  };
#  $html_trans{$FORMAT_ANSI} = $html_trans{$FORMAT_NONE};
#  $html_trans{$FORMAT_XTERM} = $html_trans{$FORMAT_NONE};
#  $html_trans{$FORMAT_GREY} = $html_trans{$FORMAT_NONE};
#  $html_trans{$FORMAT_IMC2} = $html_trans{$FORMAT_NONE};
#  $html_trans{$FORMAT_I3} = $html_trans{$FORMAT_NONE};
#  $html_trans{$FORMAT_HTML} = {
#                'BR'        => '<BR>',
#                'P'         => '<P>',
#                '/P'        => '</P>',
#                '>'         => '>',
#                'HREF'      => '<A HREF=',
#                'NAME'      => '<A NAME=',
#                '/A'        => '</A>',
#                'I'         => '<I>',
#                '/I'        => '</I>',
#                'PRE'       => '</PRE>',
#                '/PRE'      => '</PRE>',
#                'STRONG'    => '<STRONG>',
#                '/STRONG'   => '</STRONG>',
#                'TABLE'     => '<TABLE>',
#                '/TABLE'    => '</TABLE>',
#                'TR'        => '<TR>',
#                '/TR'       => '</TR>',
#                'TD'        => '<TD>',
#                '/TD'       => '</TD>',
#  };

}

sub setup_xterm {
    for( my $format = $FORMAT_FIRST; $format <= $FORMAT_LAST; $format++ ) {
        $xterm_trans{$format} = {};
        $ansi_trans{$format} = {};
        @xterm_name = ();
        @xterm_b_name = ();
        for( my $i = 0; $i < 16; $i++ ) {
            $ansi_trans{$format}{$ansi_name[$i]} = ansi2code($format,$i,0);
            $ansi_trans{$format}{"B_".$ansi_name[$i]} = ansi2code($format,$i,1);
        }
        for( my $i = 16; $i < 232; $i++ ) {
            push @xterm_name, ("F".int2base6($i));
            push @xterm_b_name, ("B".int2base6($i));
            $xterm_trans{$format}{"F".int2base6($i)} = xterm2code($format,$i,0);
            $xterm_trans{$format}{"B".int2base6($i)} = xterm2code($format,$i,1);
        }
            push @xterm_name, sprintf("G%02d", 0);
            push @xterm_b_name, sprintf("BG%02d", 0);
            $xterm_trans{$format}{sprintf("G%02d", 0)} = xterm2code($format,0,0);
            $xterm_trans{$format}{sprintf("BG%02d", 0)} = xterm2code($format,0,1);
        for( my $i = 232; $i < 256; $i++ ) {
            push @xterm_name, sprintf("G%02d", $i - 232 + 1);
            push @xterm_b_name, sprintf("BG%02d", $i - 232 + 1);
            $xterm_trans{$format}{sprintf("G%02d", $i - 232 + 1)} = xterm2code($format,$i,0);
            $xterm_trans{$format}{sprintf("BG%02d", $i - 232 + 1)} = xterm2code($format,$i,1);
        }
            push @xterm_name, sprintf("G%02d", 25);
            push @xterm_b_name, sprintf("BG%02d", 25);
            $xterm_trans{$format}{sprintf("G%02d", 25)} = xterm2code($format,15,0);
            $xterm_trans{$format}{sprintf("BG%02d", 25)} = xterm2code($format,15,1);
    }
}

sub setup_x11 {
    @x11_name = ();
    %x11_values = ();
    %x11_trans = ();
    for( my $format = $FORMAT_FIRST; $format <= $FORMAT_LAST; $format++ ) {
        $x11_trans{$format} = {};
    }
    while(<DATA>) {
        chomp;
        /^\s*(\d+)\s+(\d+)\s+(\d+)\s+(.*)$/;
        my ($r, $g, $b, $name) = ($1, $2, $3, $4);
        next if $name =~ /\s/;
        $name = ucfirst($name);
        push @x11_name, $name;
        push @x11_b_name, "B_$name";
        for( my $format = $FORMAT_FIRST; $format <= $FORMAT_LAST; $format++ ) {
            $x11_trans{$format}{$name} = rgb2code($format, $r, $g, $b, 0);
            $x11_trans{$format}{"B_".$name} = rgb2code($format, $r, $g, $b, 1);
            $x11_values{$name} = [$r, $g, $b];
            $x11_values{"B_".$name} = [$r, $g, $b];
        }
    }
}

sub setup_imc {
  # Same ordering as the @ansi_names array

    %imc_pinkfish = (
        '~~'    => '~',
        '~!'    => '%^RESET%^',
        '~L'    => '%^BOLD%^',
        '~u'    => '%^UNDERLINE%^',
        '~$'    => '%^FLASH%^',
        '~i'    => '%^ITALIC%^',
        '~v'    => '%^REVERSE%^',
        '~s'    => '%^STRIKETHRU%^',

        '~Z'    => '',
        '~x'    => '%^BLACK%^',
        '~r'    => '%^RED%^',
        '~g'    => '%^GREEN%^',
        '~y'    => '%^ORANGE%^',
        '~b'    => '%^BLUE%^',
        '~p'    => '%^MAGENTA%^',
        '~c'    => '%^CYAN%^',
        '~w'    => '%^GREY%^',
        '~z'    => '%^DARKGREY%^',
        '~R'    => '%^LIGHTRED%^',
        '~G'    => '%^LIGHTGREEN%^',
        '~Y'    => '%^YELLOW%^',
        '~B'    => '%^LIGHTBLUE%^',
        '~P'    => '%^PINK%^',
        '~C'    => '%^LIGHTCYAN%^',
        '~W'    => '%^WHITE%^',

        '~O'    => '%^ORANGE%^',
        '~m'    => '%^MAGENTA%^',
        '~d'    => '%^GREY%^',
        '~D'    => '%^DARKGREY%^',
        '~M'    => '%^PINK%^',

        '^^'    => '^',

        '^Z'    => '',
        '^x'    => '%^B_BLACK%^',
        '^r'    => '%^B_RED%^',
        '^g'    => '%^B_GREEN%^',
        '^O'    => '%^B_ORANGE%^',
        '^b'    => '%^B_BLUE%^',
        '^p'    => '%^B_MAGENTA%^',
        '^c'    => '%^B_CYAN%^',
        '^w'    => '%^B_GREY%^',
        '^z'    => '%^B_DARKGREY%^',
        '^R'    => '%^B_LIGHTRED%^',
        '^G'    => '%^B_LIGHTGREEN%^',
        '^Y'    => '%^B_YELLOW%^',
        '^B'    => '%^B_LIGHTBLUE%^',
        '^P'    => '%^B_PINK%^',
        '^C'    => '%^B_LIGHTCYAN%^',
        '^W'    => '%^B_WHITE%^',

        '^m'    => '%^B_MAGENTA%^',
        '~d'    => '%^B_GREY%^',
        '~D'    => '%^B_DARKGREY%^',
        '^M'    => '%^B_PINK%^',

        '``'    => '`',

        '`Z'    => '%^FLASH%^',
        '`x'    => '%^FLASH%^%^BLACK%^',
        '`r'    => '%^FLASH%^%^RED%^',
        '`g'    => '%^FLASH%^%^GREEN%^',
        '`O'    => '%^FLASH%^%^ORANGE%^',
        '`b'    => '%^FLASH%^%^BLUE%^',
        '`p'    => '%^FLASH%^%^MAGENTA%^',
        '`c'    => '%^FLASH%^%^CYAN%^',
        '`w'    => '%^FLASH%^%^GREY%^',
        '`z'    => '%^FLASH%^%^DARKGREY%^',
        '`R'    => '%^FLASH%^%^LIGHTRED%^',
        '`G'    => '%^FLASH%^%^LIGHTGREEN%^',
        '`Y'    => '%^FLASH%^%^YELLOW%^',
        '`B'    => '%^FLASH%^%^LIGHTBLUE%^',
        '`P'    => '%^FLASH%^%^PINK%^',
        '`C'    => '%^FLASH%^%^LIGHTCYAN%^',
        '`W'    => '%^FLASH%^%^WHITE%^',

        '`m'    => '%^FLASH%^%^MAGENTA%^',
        '`d'    => '%^FLASH%^%^GREY%^',
        '`D'    => '%^FLASH%^%^DARKGREY%^',
        '`M'    => '%^FLASH%^%^PINK%^',
    );

    %pinkfish_imc = (
        '%^BLACK%^'         => '~x',
        '%^RED%^'           => '~r',
        '%^GREEN%^'         => '~g',
        '%^ORANGE%^'        => '~y',
        '%^BLUE%^'          => '~b',
        '%^MAGENTA%^'       => '~p',
        '%^CYAN%^'          => '~c',
        '%^GREY%^'          => '~w',
        '%^DARKGREY%^'      => '~z',
        '%^LIGHTRED%^'      => '~R',
        '%^LIGHTGREEN%^'    => '~G',
        '%^YELLOW%^'        => '~Y',
        '%^LIGHTBLUE%^'     => '~B',
        '%^PINK%^'          => '~P',
        '%^LIGHTCYAN%^'     => '~C',
        '%^WHITE%^'         => '~W',

        '%^B_BLACK%^'       => '^x',
        '%^B_RED%^'         => '^r',
        '%^B_GREEN%^'       => '^g',
        '%^B_ORANGE%^'      => '^O',
        '%^B_BLUE%^'        => '^b',
        '%^B_MAGENTA%^'     => '^p',
        '%^B_CYAN%^'        => '^c',
        '%^B_GREY%^'        => '^w',
        '%^B_DARKGREY%^'    => '^z',
        '%^B_LIGHTRED%^'    => '^R',
        '%^B_LIGHTGREEN%^'  => '^G',
        '%^B_YELLOW%^'      => '^Y',
        '%^B_LIGHTBLUE%^'   => '^B',
        '%^B_PINK%^'        => '^P',
        '%^B_LIGHTCYAN%^'   => '^C',
        '%^B_WHITE%^'       => '^W',

        '%^RESET%^'         => '~!',
        '%^BOLD%^'          => '~L',
        '%^UNDERLINE%^'     => '~u',
        '%^FLASH%^'         => '~$',
        '%^ITALIC%^'        => '~i',
        '%^REVERSE%^'       => '~v',
        '%^STRIKETHRU%^'    => '~s',
    );

    %pinkfish_imc_cleanup = (
        '%^RESET'           => '~!',
        '%^BOLD'            => '~L',
        '%^UNDERLINE'       => '~u',
        '%^FLASH'           => '~$',
        '%^ITALIC'          => '~i',
        '%^REVERSE'         => '~v',
        '%^STRIKETHRU'      => '~s',

        'RESET%^'           => '~!',
        'BOLD%^'            => '~L',
        'UNDERLINE%^'       => '~u',
        'FLASH%^'           => '~$',
        'ITALIC%^'          => '~i',
        'REVERSE%^'         => '~v',
        'STRIKETHRU%^'      => '~s',
    );

    %pinkfish_imc_ext = ();

    for( my $i = 16; $i < 232; $i++ ) {
        my $token = int2base6($i);
        my $ansi = xterm2ansi($i);
        $pinkfish_imc_ext{"%^F$token%^"} = $imc_tokens[$ansi];
        $pinkfish_imc_ext{"%^B$token%^"} = $imc_back_tokens[$ansi];
    }
    for( my $i = 232; $i < 256; $i++ ) {
        my $token = sprintf("%02d", $i - 232 + 1);
        my $ansi = xterm2ansi($i);
        $pinkfish_imc_ext{"%^G$token%^"} = $imc_tokens[$ansi];
        $pinkfish_imc_ext{"%^BG$token%^"} = $imc_back_tokens[$ansi];
    }

    foreach my $k (sort keys %x11_values) {
        my ($r,$g,$b) = @{ $x11_values{$k} };
        my $ansi = rgb2ansi($r, $g, $b);
        $pinkfish_imc_ext{"%^$k%^"} = $imc_tokens[$ansi];
    }
}

# This is a generic euclidean distance formula, used to determine
# how "close" one set of RGB values is to another.  The weight
# factors default to 1.0, but have suggested values based on the
# human eye sensitivity, should you want to skew things for a
# more artistic purpose.
sub rgb_distance {
    my ($r1, $g1, $b1) = (shift, shift, shift);
    my ($r2, $g2, $b2) = (shift, shift, shift);

    my ($rf, $gf, $bf);
    my ($dr, $dg, $db, $dist);

    $rf = 1.0; # 0.241
    $gf = 1.0; # 0.691
    $bf = 1.0; # 0.068

    $dr = abs($r2 - $r1) ;
    $dg = abs($g2 - $g1);
    $db = abs($b2 - $b1);
    $dist = sqrt(($dr * $dr * $rf) + ($dg * $dg * $gf) + ($db * $db * $bf));

    return $dist;
}

# This accepts a standard ANSI value from 0 to 15, and
# returns an array of RGB values.
sub ansi2rgb {
    my $colour = shift;
    my ($red, $green, $blue);

    $red = $ansi[$colour][0];
    $green = $ansi[$colour][1];
    $blue = $ansi[$colour][2];

    return [ $red, $green, $blue ];
}

# This accepts an XTERM-256 slot number from 0 to 255,
# and returns an array of RGB values.
sub xterm2rgb {
    my $colour = shift;
    my ($red, $green, $blue);

    if( $colour < 16 ) {
        $red = $ansi[$colour][0];
        $green = $ansi[$colour][1];
        $blue = $ansi[$colour][2];
    } elsif ( $colour < 232 ) {
        $colour -= 16;
        $red = $hexes[($colour/36)%6];
        $green = $hexes[($colour/6)%6];
        $blue = $hexes[($colour)%6];
    } else {
        $colour -= 232;
        $red = $green = $blue = $greys[$colour%24];
    }

    return [ $red, $green, $blue ];
}

# This function finds the closest matching "xterm" colour
# for the given 8-bit RGB values.  This is the workhorse
# function which accepts ranges to allow specifying what
# portion of the xterm colour pallete to use for matches.
#
# There are three API functions to find best match,
# best ANSI match, and best greyscale match.
#
# Returns -1 on failure.
sub rgb2match {
    my ($r1, $g1, $b1, $low, $high) = (shift, shift, shift, shift, shift);
    my $i;
    my ($r2, $g2, $b2);
    my $match;
    my ($max_distance, $dist);
    my $tmp;
  
    $match = -1;
    $max_distance = 10000000000.0;
  
    for($i=$low; $i<=$high; $i++) {
      $tmp = xterm2rgb($i);
      $r2 = $tmp->[0];
      $g2 = $tmp->[1];
      $b2 = $tmp->[2];
      $dist = rgb_distance($r1,$g1,$b1,$r2,$g2,$b2);
  
      if($dist < $max_distance) {
        $max_distance = $dist;
        $match = $i;
      }
    }
  
    return $match;
}

# The following are just helper functions that
# call rgb2match() with the correct parameters.
sub rgb2ansi {
    my ($r1, $g1, $b1) = (shift, shift, shift);
    return rgb2match( $r1, $g1, $b1, 0, 15 );
}

sub rgb2xterm {
    my ($r1, $g1, $b1) = (shift, shift, shift);
    return rgb2match( $r1, $g1, $b1, 0, 255 );
}

sub rgb2grey {
    my ($r1, $g1, $b1) = (shift, shift, shift);
    return rgb2match( $r1, $g1, $b1, 232, 255 );
}

# This function finds the closest ANSI
# colour to the XTERM-256 colour provided.
sub xterm2ansi {
    my $xterm = shift;
    my $rgb;

    $rgb = xterm2rgb( $xterm );
    return rgb2ansi( $rgb->[0], $rgb->[1], $rgb->[2] );
}

# This takes an extended ANSI colour and returns the
# escape code string.  Valid colours are 0-15, with the
# colours 8-15 being BOLD versions of 0-7.
sub ansi2code {
    my ($format, $colour, $background) = (shift, shift, shift);
    my $result;
    my $rgb;
    my $code;
    my $bold;

    $result = "";
    $bold = 0;
    if( $format == $FORMAT_NONE ) {
        $result = "";
    } elsif( $format == $FORMAT_ANSI ) {
        if( $colour > 7 ) {
            $bold = 1;
            $colour -= 8;
        }
        if( $background ) {
            $result .= "\033[" . (40 + $colour) . "m";
        } else {
            $result .= "\033[" . ($bold ? "1;" : "") . (30 + $colour) . "m";
        }
    } elsif( $format == $FORMAT_XTERM ) {
        $result = "\033[" . ( $background == 1 ? 48 : 38 ) . ";5;" . $colour . "m";
    } elsif( $format == $FORMAT_GREY ) {
        $rgb = ansi2rgb( $colour );
        $code = rgb2grey( $rgb->[0], $rgb->[1], $rgb->[2] );
        $result = "\033[" . ( $background == 1 ? 48 : 38 ) . ";5;" . $code . "m";
    } elsif( $format == $FORMAT_IMC2 ) {
        if( $background ) {
            $result = $imc_back_tokens[$colour];
        } else {
            $result = $imc_tokens[$colour];
        }
    } elsif( $format == $FORMAT_I3 ) {
        if( $background ) {
            $result = $old_b_pinkfish[$colour];
        } else {
            $result = $old_pinkfish[$colour];
        }
    } elsif( $format == $FORMAT_MXP ) {
        $rgb = ansi2rgb( $colour );
        if( $background ) {
            $result = sprintf("<COLOR BACK=\"#%02x%02x%02x\">", $rgb->[0], $rgb->[1], $rgb->[2]);
        } else {
            $result = sprintf("<COLOR FORE=\"#%02x%02x%02x\">", $rgb->[0], $rgb->[1], $rgb->[2]);
        }
#    } elsif( $format == $FORMAT_HTML ) {
#        $rgb = ansi2rgb( $colour );
#        $result = "<SPAN style=\"";
#        $result .= $background ? "background-" : "";
#        $result .= "color: #";
#        $result .= int2hex( $rgb->[0] );
#        $result .= int2hex( $rgb->[1] );
#        $result .= int2hex( $rgb->[2] );
#        $result .= "\">";
#        # For this to work, we also need a closing token
    }
    return $result;
}

# This takes an XTERM-256 slot number and returns the
# escape code string.  Valid codes are 0-255.
sub xterm2code {
    my ($format, $colour, $background) = (shift, shift, shift);
    my $result;
    my $rgb;
    my $code;
    my $bold;

    $result = "";
    $bold = 0;
    if( $format == $FORMAT_NONE ) {
        $result = "";
    } elsif( $format == $FORMAT_ANSI ) {
        $code = xterm2ansi($colour);
        if( $code > 7 ) {
            $bold = 1;
            $code -= 8;
        }
        if( $background ) {
            $result .= "\033[" . (40 + $code) . "m";
        } else {
            $result .= "\033[" . ($bold ? "1;" : "") . (30 + $code) . "m";
        }
    } elsif( $format == $FORMAT_XTERM ) {
        $result = "\033[" . ( $background == 1 ? 48 : 38 ) . ";5;" . $colour . "m";
    } elsif( $format == $FORMAT_GREY ) {
        $rgb = xterm2rgb( $colour );
        $code = rgb2grey( $rgb->[0], $rgb->[1], $rgb->[2] );
        $result = "\033[" . ( $background == 1 ? 48 : 38 ) . ";5;" . $code . "m";
    } elsif( $format == $FORMAT_IMC2 ) {
        $code = xterm2ansi($colour);
        if( $background ) {
            $result = $imc_back_tokens[$code];
        } else {
            $result = $imc_tokens[$code];
        }
    } elsif( $format == $FORMAT_I3 ) {
        $code = xterm2ansi($colour);
        if( $background ) {
            $result = $old_b_pinkfish[$code];
        } else {
            $result = $old_pinkfish[$code];
        }
    } elsif( $format == $FORMAT_MXP ) {
        $rgb = xterm2rgb( $colour );
        if( $background ) {
            $result = sprintf("<COLOR BACK=\"#%02x%02x%02x\">", $rgb->[0], $rgb->[1], $rgb->[2]);
        } else {
            $result = sprintf("<COLOR FORE=\"#%02x%02x%02x\">", $rgb->[0], $rgb->[1], $rgb->[2]);
        }
#    } elsif( $format == $FORMAT_HTML ) {
#        $rgb = xterm2rgb( $colour );
#        $result = "<SPAN style=\"";
#        $result .= $background ? "background-" : "";
#        $result .= "colour: #";
#        $result .= int2hex( $rgb->[0] );
#        $result .= int2hex( $rgb->[1] );
#        $result .= int2hex( $rgb->[2] );
#        $result .= "\">";
#        # For this to work, we also need a closing token
    }
    return $result;
}


# This takes red, green, and blue colour values and returns the
# escape code string.  Valid codes are 0-255.
sub rgb2code {
    my ($format, $r, $g, $b, $background) = (shift, shift, shift, shift, shift);
    my $result;
    my $code;
    my $bold;

    $result = "";
    $bold = 0;

    if( $format == $FORMAT_NONE ) {
        $result = "";
    } elsif( $format == $FORMAT_ANSI ) {
        $code = rgb2ansi($r, $g, $b);
        if( $code > 7 ) {
            $bold = 1;
            $code -= 8;
        }
        if( $background ) {
            $result .= "\033[" . (40 + $code) . "m";
        } else {
            $result .= "\033[" . ($bold ? "1;" : "") . (30 + $code) . "m";
        }
    } elsif( $format == $FORMAT_XTERM ) {
        $code = rgb2xterm($r, $g, $b);
        $result = "\033[" . ( $background == 1 ? 48 : 38 ) . ";5;" . $code . "m";
    } elsif( $format == $FORMAT_GREY ) {
        $code = rgb2grey( $r, $g, $b );
        $result = "\033[" . ( $background == 1 ? 48 : 38 ) . ";5;" . $code . "m";
    } elsif( $format == $FORMAT_IMC2 ) {
        $code = rgb2ansi($r, $g, $b);
        if( $background ) {
            $result = $imc_back_tokens[$code];
        } else {
            $result = $imc_tokens[$code];
        }
    } elsif( $format == $FORMAT_I3 ) {
        $code = rgb2ansi($r, $g, $b);
        if( $background ) {
            $result = $old_b_pinkfish[$code];
        } else {
            $result = $old_pinkfish[$code];
        }
    } elsif( $format == $FORMAT_MXP ) {
        if( $background ) {
            $result = sprintf("<COLOR BACK=\"#%02x%02x%02x\">", $r, $g, $b);
        } else {
            $result = sprintf("<COLOR FORE=\"#%02x%02x%02x\">", $r, $g, $b);
        }
#    } elsif( $format == $FORMAT_HTML ) {
#        $result = "<SPAN style=\"";
#        $result .= $background ? "background-" : "";
#        $result .= "colour: #";
#        $result .= int2hex( $r );
#        $result .= int2hex( $g );
#        $result .= int2hex( $b );
#        $result .= "\">";
#        # For this to work, we also need a closing token
    }
    return $result;
}

sub xterm_colours {
  my $i;
  my $output;
  my $xterm_text;
  my $token;

  $output = "";
  $xterm_text = "";
  for( $i = 0; $i < 16; $i++ ) {
    if( $i > 0 && !( $i % 4 ) ) {
      $output = $output . $xterm_text . "\n";
      $xterm_text = "";
    }
    $token = $ansi_name[$i];
    $xterm_text = $xterm_text . "%^" . $token . "%^" . $token . "%^RESET%^ ";
  }
  $output = $output . $xterm_text . "\n\n";
  $xterm_text = "";

  for( $i = 16; $i < 232; $i++ ) {
    if( ($i-16) > 0 && !( ($i-16) % 6 ) ) {
      $output = $output . $xterm_text . "\n";
      $xterm_text = "";
    }
    $token = int2base6($i);
    $xterm_text = $xterm_text . "%^F" . $token . "%^F" . $token . "%^RESET%^ ";
  }
  $output = $output . $xterm_text . "\n\n";
  $xterm_text = "";

  for( $i = 232; $i < 256; $i++ ) {
    if( ($i-232) > 0 && !( ($i-232) % 4 ) ) {
      $output = $output . $xterm_text . "\n";
      $xterm_text = "";
    }
    $token = sprintf("%02d", $i - 232 + 1);
    $xterm_text = $xterm_text . "%^G" . $token . "%^G" . $token . "%^RESET%^ ";
  }
  $output = $output . $xterm_text . "\n\n";
  $xterm_text = "";
  return $output;
}

sub x11_colours {
    my $output = "";
    my $x11_text = "";
    my $i = 0;

    foreach (@x11_name) {
        if( $i > 0 && !( $i % 4 ) ) {
            $output = $output . $x11_text . "\n";
            $x11_text = "";
        }
        $x11_text .= "%^$_%^: $_%^RESET%^ ";
    }
    $output .= "$x11_text\n";
    return $output;
}




sub output_dump {
    my $file = shift;
    my $out = "";

    $out .= "ANSI names: " . Dumper(\@ansi_name) . "\n";
    $out .= "ANSI BACK names: " . Dumper(\@ansi_b_name) . "\n";
    $out .= "XTERM names: " . Dumper(\@xterm_name) . "\n";
    $out .= "XTERM BACK names: " . Dumper(\@xterm_b_name) . "\n";
    $out .= "X11 names: " . Dumper(\@x11_name) . "\n";
    $out .= "X11 BACK names: " . Dumper(\@x11_b_name) . "\n";
    $out .= "ANSI trans: " . Dumper(\%ansi_trans) . "\n";
    $out .= "XTERM trans: " . Dumper(\%xterm_trans) . "\n";
    $out .= "X11 trans: " . Dumper(\%x11_trans) . "\n";

    if(defined $file) {
        open FOO, ">$file" or die "Cannot open output $file";
        print FOO "$out\n";
        close FOO;
    } else {
        print "$out\n";
    }
}

sub output_perl {
    my $file = shift;
    my $out = "";

    $out .= "#!/usr/bin/perl -w\n";
    $out .= "use Data::Dumper;\n";
    $out .= "my \%term_info = ();\n";
    for( my $format = $FORMAT_FIRST; $format <= $FORMAT_LAST; $format++ ) {
        $out .= sprintf "    \$term_info{\"%s\"} =\n", $term_name[$format];
        $out .= "        {\n";
        foreach my $name (sort keys %{$attr_trans{$format}}) {
            my $code = $attr_trans{$format}{$name};
            $code =~ s/\"/\\\"/g;
            $code =~ s/\033/\\033/g;
            $out .= sprintf "            %-24s => %s,\n", "\"$name\"", "\"$code\"";
        }
        $out .= sprintf "\n";
        foreach my $name (sort keys %{$terminal_trans{$format}}) {
            my $code = $terminal_trans{$format}{$name};
            $code =~ s/\"/\\\"/g;
            $code =~ s/\033/\\033/g;
            $out .= sprintf "            %-24s => %s,\n", "\"$name\"", "\"$code\"";
        }
        $out .= "\n";
#        foreach my $name (sort keys %{$html_trans{$format}}) {
#            $out .= sprintf "            %-24s => %s,\n", "\"$name\"", "\"$html_trans{$format}{$name}\"";
#        }
        foreach my $background (0, 1) {
            $out .= "\n";
            for(my $i = 0; $i < scalar(@ansi_name); $i++) {
                my $name = $background ? $ansi_b_name[$i]: $ansi_name[$i];
                my $code = $ansi_trans{$format}{$name};
                $code =~ s/\"/\\\"/g;
                $code =~ s/\033/\\033/g;
                $out .= sprintf "            %-24s => %s,\n", "\"$name\"", "\"$code\"";
            }
        }
        foreach my $background (0, 1) {
            $out .= "\n";
            for(my $i = 0; $i < scalar(@xterm_name); $i++) {
                my $name = $background ? $xterm_b_name[$i]: $xterm_name[$i];
                my $code = $xterm_trans{$format}{$name};
                $code =~ s/\"/\\\"/g;
                $code =~ s/\033/\\033/g;
                $out .= sprintf "            %-24s => %s,\n", "\"$name\"", "\"$code\"";
            }
        }
        foreach my $background (0, 1) {
            $out .= "\n";
            for(my $i = 0; $i < scalar(@x11_name); $i++) {
                my $name = $background ? $x11_b_name[$i]: $x11_name[$i];
                my $code = $x11_trans{$format}{$name};
                $code =~ s/\"/\\\"/g;
                $code =~ s/\033/\\033/g;
                $out .= sprintf "            %-24s => %s,\n", "\"$name\"", "\"$code\"";
            }
        }
        $out .= "        };\n";
    }
    $out .= <<'EOM'

    sub parse {
        my $term = shift;
        my $str = shift;

        #printf "TERM: %s\n", $term;
        my @bits = split( /\%\^/, $str );
        for( my $i = 0; $i < scalar(@bits); $i++ ) {
            next if !defined $bits[$i];
            next if $bits[$i] eq "";
            #printf("OLD BITS: %s", Dumper($bits[$i]));
            if( exists $term_info{$term}{ $bits[$i] } ) {
                $bits[$i] = $term_info{$term}{ $bits[$i] };
                #printf("NEW BITS: %s", Dumper($bits[$i]));
            }
        }
        return join( '', @bits );
    }

#    sub GetHTML {
#        my $str = shift;
#        my ($o, $c) = (0,0);
#        my $tmp = "";
#
#        $str = parse( "html", $str );
#        while( $str =~ /\G<SPAN/cmgsx ) {
#            $o++;
#        }
#        return $str if $o < 1;
#
#        $tmp = $str;
#        while( $str =~ /\G<SPAN/cmgsx ) {
#            $c++;
#        }
#        for($i = $o - $c; $i > 0; $i--) {
#            $str .= "</SPAN>";
#        }
#        return $str;
#    }

    my $test = '%^RED%^Hello %^SeaGreen3%^there foolish mortal!%^RESET%^';

    print parse("unknown", "$test\n");
    print parse("ansi", "$test\n");
    print parse("xterm-256color", "$test\n");
    print parse("xterm-grey", "$test\n");
#    print parse("html", "$test\n");
#    print GetHTML($test) . "\n";
EOM
    ;

    if(defined $file) {
        open FOO, ">$file" or die "Cannot open output $file";
        print FOO "$out\n";
        close FOO;
    } else {
        print "$out\n";
    }
}

sub output_lpc {
    my $file = shift;
    my $out = "";

    $out .= <<'EOM'
// Various Pinkfish conversion mappings

#define EXTENDED_PINKFISH 1

#ifdef TERMINAL_C
static mapping term_info = ([
EOM
    ;

    for( my $format = $FORMAT_FIRST; $format <= $FORMAT_LAST; $format++ ) {
        $out .= sprintf "    \"%s\" : ([\n", $term_name[$format];

        foreach my $name (sort keys %{$attr_trans{$format}}) {
            my $code = $attr_trans{$format}{$name};
            $code =~ s/\"/\\\"/g;
            $code =~ s/\033/\\033/g;
            $out .= sprintf "            %-24s : %s,\n", "\"$name\"", "\"$code\"";
        }
        $out .= sprintf "\n";
        foreach my $name (sort keys %{$terminal_trans{$format}}) {
            my $code = $terminal_trans{$format}{$name};
            $code =~ s/\"/\\\"/g;
            $code =~ s/\033/\\033/g;
            $out .= sprintf "            %-24s : %s,\n", "\"$name\"", "\"$code\"";
        }
        $out .= "\n";
#        foreach my $name (sort keys %{$html_trans{$format}}) {
#            $out .= sprintf "            %-24s : %s,\n", "\"$name\"", "\"$html_trans{$format}{$name}\"";
#        }
        foreach my $background (0, 1) {
            $out .= "\n";
            for(my $i = 0; $i < scalar(@ansi_name); $i++) {
                my $name = $background ? $ansi_b_name[$i]: $ansi_name[$i];
                my $code = $ansi_trans{$format}{$name};
                $code =~ s/\"/\\\"/g;
                $code =~ s/\033/\\033/g;
                $out .= sprintf "            %-24s : %s,\n", "\"$name\"", "\"$code\"";
            }
        }
        $out .= "#ifdef EXTENDED_PINKFISH\n";
        foreach my $background (0, 1) {
            $out .= "\n";
            for(my $i = 0; $i < scalar(@xterm_name); $i++) {
                my $name = $background ? $xterm_b_name[$i]: $xterm_name[$i];
                my $code = $xterm_trans{$format}{$name};
                $code =~ s/\"/\\\"/g;
                $code =~ s/\033/\\033/g;
                $out .= sprintf "            %-24s : %s,\n", "\"$name\"", "\"$code\"";
            }
        }
        foreach my $background (0, 1) {
            $out .= "\n";
            for(my $i = 0; $i < scalar(@x11_name); $i++) {
                my $name = $background ? $x11_b_name[$i]: $x11_name[$i];
                my $code = $x11_trans{$format}{$name};
                $code =~ s/\"/\\\"/g;
                $code =~ s/\033/\\033/g;
                $out .= sprintf "            %-24s : %s,\n", "\"$name\"", "\"$code\"";
            }
        }
        $out .= "#endif // EXTENDED_PINKFISH\n";
        $out .= sprintf "    ]),\n";
    }
    #chop $out; chop $out; # Remove ",\n" from the last element.
    #$out .= "\n";
    $out .= <<'EOM'
]);
#endif // TERMINAL_C

#ifdef IMC2_C
static private mapping imc_pinkfish = ([
EOM
    ;

    foreach my $k (sort keys %imc_pinkfish) {
        $out .= sprintf "            %-24s : %s,\n", "\"$k\"", "\"$imc_pinkfish{$k}\"";
    }

    $out .= <<'EOM'
]);

static private mapping pinkfish_imc = ([
EOM
    ;

    foreach my $k (sort keys %pinkfish_imc) {
        $out .= sprintf "            %-24s : %s,\n", "\"$k\"", "\"$pinkfish_imc{$k}\"";
    }
    $out .= "#ifdef EXTENDED_PINKFISH\n";
    foreach my $k (sort keys %pinkfish_imc_ext) {
        $out .= sprintf "            %-24s : %s,\n", "\"$k\"", "\"$pinkfish_imc_ext{$k}\"";
    }
    $out .= "#endif // EXTENDED_PINKFISH\n";

    $out .= <<'EOM'
]);

static private mapping pinkfish_imc_cleanup = ([
EOM
    ;

    foreach my $k (sort keys %pinkfish_imc_cleanup) {
        $out .= sprintf "            %-24s : %s,\n", "\"$k\"", "\"$pinkfish_imc_cleanup{$k}\"";
    }

    $out .= <<'EOM'
]);
#endif // IMC2_C

#ifdef CHANNEL_C

static private mapping extended_to_pinkfish = ([

EOM
    ;

    for( my $i = 16; $i < 232; $i++ ) {
        my $token = int2base6($i);
        my $x = xterm2ansi($i);
        my $ansi = $ansi_name[$x];
        my $bansi = $ansi_b_name[$x];
        $out .= sprintf "            %-24s : %s,\n", "\"%^F$token%^\"", "\"%^$ansi%^\"";
        $out .= sprintf "            %-24s : %s,\n", "\"%^B$token%^\"", "\"%^$bansi%^\"";
    }
    {
        my $token = sprintf("%02d", 0);
        my $x = xterm2ansi(0);
        my $ansi = $ansi_name[$x];
        my $bansi = $ansi_b_name[$x];
        $out .= sprintf "            %-24s : %s,\n", "\"%^G$token%^\"", "\"%^$ansi%^\"";
        $out .= sprintf "            %-24s : %s,\n", "\"%^BG$token%^\"", "\"%^$bansi%^\"";
    }
    for( my $i = 232; $i < 256; $i++ ) {
        my $token = sprintf("%02d", $i - 232 + 1);
        my $x = xterm2ansi($i);
        my $ansi = $ansi_name[$x];
        my $bansi = $ansi_b_name[$x];
        $out .= sprintf "            %-24s : %s,\n", "\"%^G$token%^\"", "\"%^$ansi%^\"";
        $out .= sprintf "            %-24s : %s,\n", "\"%^BG$token%^\"", "\"%^$bansi%^\"";
    }
    {
        my $token = sprintf("%02d", 25);
        my $x = xterm2ansi(15);
        my $ansi = $ansi_name[$x];
        my $bansi = $ansi_b_name[$x];
        $out .= sprintf "            %-24s : %s,\n", "\"%^G$token%^\"", "\"%^$ansi%^\"";
        $out .= sprintf "            %-24s : %s,\n", "\"%^BG$token%^\"", "\"%^$bansi%^\"";
    }

    foreach my $k (sort keys %x11_values) {
        my ($r,$g,$b) = @{ $x11_values{$k} };
        my $ansi = $ansi_name[rgb2ansi($r, $g, $b)];
        $out .= sprintf "            %-24s : %s,\n", "\"%^$k%^\"", "\"%^$ansi%^\"";
    }

    $out .= <<'EOM'
]);

#endif // CHANNEL_C

EOM
    ;

    if(defined $file) {
        open FOO, ">$file" or die "Cannot open output $file";
        print FOO "$out\n";
        close FOO;
    } else {
        print "$out\n";
    }
}

sub output_cpp {
    my $file = shift;
    my $out = "";
    my $format_count = $FORMAT_LAST - $FORMAT_FIRST + 1;

    $out .= <<EOM
#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <ctype.h>
#include <string.h>
#include <string>
#include <map>

//typedef std::map<const char *, const char *> stringMap;
//typedef std::map<const char *, stringMap> stringMapMap;
typedef std::map<std::string, std::string> stringMap;
typedef std::map<std::string, stringMap> stringMapMap;

class transMap {
    public:
        transMap();
        const char * color_translate( const char *terminal, const char *symbol );
    private:
        stringMapMap trans_map;
        stringMap imc_pinkfish;
        stringMap pinkfish_imc;
        stringMap pinkfish_imc_cleanup;
        stringMap extended_to_pinkfish;
        void setupTransMap( void );
        void setupImcPinkfishMap( void );
        void setupPinkfishImcMap( void );
        void setupPinkfishImcCleanupMap( void );
        void setupExtendedToPinkfishMap( void );
        int * xterm2rgb( int color );
        float rgb_distance(int r1, int g1, int b1, int r2, int g2, int b2);
        int rgb2match( int r1, int g1, int b1, int low, int high );
        int rgb2ansi( int r1, int g1, int b1 );
        int rgb2xterm( int r1, int g1, int b1 );
        int rgb2grey( int r1, int g1, int b1 );
        char * slot2xterm( int xterm, int bg );
        char * rgb2xterm256(const char * rgb, int bg);
};

EOM
    ;

    {
        my %meta_trans = ();
        for( my $format = $FORMAT_FIRST; $format <= $FORMAT_LAST; $format++ ) {
            $meta_trans{$format} = ();
            $meta_trans{$format}{$_} = $attr_trans{$format}{$_} foreach (keys %{ $attr_trans{$format} });
            $meta_trans{$format}{$_} = $terminal_trans{$format}{$_} foreach (keys %{ $terminal_trans{$format} });
            $meta_trans{$format}{$_} = $ansi_trans{$format}{$_} foreach (keys %{ $ansi_trans{$format} });
            $meta_trans{$format}{$_} = $xterm_trans{$format}{$_} foreach (keys %{ $xterm_trans{$format} });
            $meta_trans{$format}{$_} = $x11_trans{$format}{$_} foreach (keys %{ $x11_trans{$format} });
        }

        $out .= "void transMap::setupTransMap( void ) {\n";
        for( my $format = $FORMAT_FIRST; $format <= $FORMAT_LAST; $format++ ) {
            foreach my $k (sort keys %{ $meta_trans{$format} }) {
                my $v = $meta_trans{$format}{$k};
                $v =~ s/\"/\\\"/g;
                $v =~ s/\033/\\033/g;
                $out .= sprintf "    trans_map[\"%s\"][\"%s\"] = \"%s\";\n", $term_name[$format], $k, $v;
            }
        }
        $out .= "}\n\n";
    }

    {
        $out .= "void transMap::setupImcPinkfishMap( void ) {\n";
        {
            foreach my $k (sort keys %imc_pinkfish) {
                my $v = $imc_pinkfish{$k};
                $v =~ s/\"/\\\"/g;
                $v =~ s/\033/\\033/g;
                $out .= sprintf "    imc_pinkfish[\"%s\"] = \"%s\";\n", $k, $v;
            }
        }
        $out .= "}\n\n";
    }

    {
        my %meta = ();
        $meta{$_} = $pinkfish_imc{$_} foreach (keys %pinkfish_imc);
        $meta{$_} = $pinkfish_imc_ext{$_} foreach (keys %pinkfish_imc_ext);

        $out .= "void transMap::setupPinkfishImcMap( void ) {\n";
        {
            foreach my $k (sort keys %meta) {
                my $v = $meta{$k};
                $v =~ s/\"/\\\"/g;
                $v =~ s/\033/\\033/g;
                $out .= sprintf "    pinkfish_imc[\"%s\"] = \"%s\";\n", $k, $v;
            }
        }
        $out .= "}\n\n";
    }

    {
        $out .= "void transMap::setupPinkfishImcCleanupMap( void ) {\n";
        {
            foreach my $k (sort keys %pinkfish_imc_cleanup) {
                my $v = $pinkfish_imc_cleanup{$k};
                $v =~ s/\"/\\\"/g;
                $v =~ s/\033/\\033/g;
                $out .= sprintf "    pinkfish_imc_cleanup[\"%s\"] = \"%s\";\n", $k, $v;
            }
        }
        $out .= "}\n\n";
    }

    {
        my %meta = ();
        for( my $i = 16; $i < 232; $i++ ) {
            my $token = int2base6($i);
            my $x = xterm2ansi($i);
            my $ansi = $ansi_name[$x];
            my $bansi = $ansi_b_name[$x];
            $meta{"%^F$token%^"} = "%^$ansi%^";
            $meta{"%^B$token%^"} = "%^$bansi%^";
        }
        {
            my $token = sprintf("%02d", 0);
            my $x = xterm2ansi(0);
            my $ansi = $ansi_name[$x];
            my $bansi = $ansi_b_name[$x];
            $meta{"%^G$token%^"} = "%^$ansi%^";
            $meta{"%^BG$token%^"} = "%^$bansi%^";
        }
        for( my $i = 232; $i < 256; $i++ ) {
            my $token = sprintf("%02d", $i - 232 + 1);
            my $x = xterm2ansi($i);
            my $ansi = $ansi_name[$x];
            my $bansi = $ansi_b_name[$x];
            $meta{"%^G$token%^"} = "%^$ansi%^";
            $meta{"%^BG$token%^"} = "%^$bansi%^";
        }
        {
            my $token = sprintf("%02d", 25);
            my $x = xterm2ansi(15);
            my $ansi = $ansi_name[$x];
            my $bansi = $ansi_b_name[$x];
            $meta{"%^G$token%^"} = "%^$ansi%^";
            $meta{"%^BG$token%^"} = "%^$bansi%^";
        }

        foreach my $k (sort keys %x11_values) {
            my ($r,$g,$b) = @{ $x11_values{$k} };
            my $ansi = $ansi_name[rgb2ansi($r, $g, $b)];
            $meta{"%^$k%^"} = "%^$ansi%^";
        }
        my $meta_count = scalar keys %meta;

        $out .= "void transMap::setupExtendedToPinkfishMap( void ) {\n";
        foreach my $k (sort keys %meta) {
            my $v = $meta{$k};
            $out .= sprintf "    extended_to_pinkfish[\"%s\"] = \"%s\";\n", $k, $v;
        }
        $out .= "}\n\n";
    }

    $out .= <<EOM
transMap::transMap() {
    setupTransMap();
    setupImcPinkfishMap();
    setupPinkfishImcMap();
    setupPinkfishImcCleanupMap();
    setupExtendedToPinkfishMap();
}

static const int hex_rgb[6] = { 0x00, 0x55, 0x88, 0xBB, 0xDD, 0xFF };

static const int ansi_rgb[16][3] = {
    { 0x00, 0x00, 0x00 }, /*  0  black */
    { 0xBB, 0x00, 0x00 }, /*  1  red */
    { 0x00, 0xBB, 0x00 }, /*  2  green */
    { 0xBB, 0xBB, 0x00 }, /*  3  yellow/orange */
    { 0x00, 0x00, 0xBB }, /*  4  blue */
    { 0xBB, 0x00, 0xBB }, /*  5  magenta */
    { 0x00, 0xBB, 0xBB }, /*  6  cyan */
    { 0xBB, 0xBB, 0xBB }, /*  7  light grey */

    { 0x55, 0x55, 0x55 }, /*  8  dark grey */
    { 0xFF, 0x55, 0x55 }, /*  9  bright red */
    { 0x55, 0xFF, 0x55 }, /* 10  bright green */
    { 0xFF, 0xFF, 0x55 }, /* 11  yellow */
    { 0x55, 0x55, 0xFF }, /* 12  bright blue */
    { 0xFF, 0x55, 0xFF }, /* 13  bright magenta */
    { 0x55, 0xFF, 0xFF }, /* 14  bright cyan */
    { 0xFF, 0xFF, 0xFF }  /* 15  white */
};

static const char * ansi_name[16] = {
    "BLACK", "RED", "GREEN", "ORANGE",
    "BLUE", "MAGENTA", "CYAN", "GREY",
    "DARKGREY", "LIGHTRED", "LIGHTGREEN", "YELLOW",
    "LIGHTBLUE", "PINK", "LIGHTCYAN", "WHITE"
};

static const int grey_rgb[24] = {
    0x08, 0x12, 0x1C, 0x26, 0x30, 0x3A, 
    0x44, 0x4E, 0x58, 0x62, 0x6C, 0x76, 
    0x80, 0x8A, 0x94, 0x9E, 0xA8, 0xB2, 
    0xBC, 0xC6, 0xD0, 0xDA, 0xE4, 0xEE
};

/*
 * This accepts an XTERM-256 slot number from 0 to 255,
 * and returns an array of RGB values.
 */
int * transMap::xterm2rgb( int color )
{
  int red, green, blue;
  static int retval[3];

  if( color < 16 ) {
        red = ansi_rgb[color][0];
        green = ansi_rgb[color][1];
        blue = ansi_rgb[color][2];
  } else if ( color < 232 ) {
        color -= 16;
        red = hex_rgb[(color/36)%6];
        green = hex_rgb[(color/6)%6];
        blue = hex_rgb[(color)%6];
  } else {
        color -= 232;
        red = green = blue = grey_rgb[color%24];
  }

  retval[0] = red;
  retval[1] = green;
  retval[2] = blue;
  return retval;
}

/*
 * This is a generic euclidean distance formula, used to determine
 * how "close" one set of RGB values is to another.  The weight
 * factors default to 1.0, but have suggested values based on the
 * human eye sensitivity, should you want to skew things for a
 * more artistic purpose.
 */
float transMap::rgb_distance(int r1, int g1, int b1, int r2, int g2, int b2)
{
  float rf, gf, bf;
  float dr, dg, db, dist;

  rf = 1.0; /* 0.241 */
  gf = 1.0; /* 0.691 */
  bf = 1.0; /* 0.068 */

  dr = abs(r2 - r1);
  dg = abs(g2 - g1);
  db = abs(b2 - b1);
  dist = sqrt((dr * dr * rf) + (dg * dg * gf) + (db * db * bf));

  return dist;
}

/*
 * This function finds the closest matching "xterm" colour
 * for the given 8-bit RGB values.  This is the workhorse
 * function which accepts ranges to allow specifying what
 * portion of the xterm colour pallete to use for matches.
 *
 * There are three API functions to find best match,
 * best ANSI match, and best greyscale match.
 *
 * Returns -1 on failure.
 */
int transMap::rgb2match( int r1, int g1, int b1, int low, int high )
{
  int i;
  int r2, g2, b2;
  int match;
  float max_distance, dist;
  int * tmp;

  match = -1;
  max_distance = 10000000000.0;

  for(i=low; i<=high; i++) {
    tmp = xterm2rgb(i);
    r2 = tmp[0];
    g2 = tmp[1];
    b2 = tmp[2];
    dist = rgb_distance(r1,g1,b1,r2,g2,b2);

    if(dist < max_distance) {
      max_distance = dist;
      match = i;
    }
  }

  return match;
}

/*
 * The following are just helper functions that
 * call rgb2match() with the correct parameters.
 */
int transMap::rgb2ansi( int r1, int g1, int b1 )
{
  return rgb2match( r1, g1, b1, 0, 15 );
}

int transMap::rgb2xterm( int r1, int g1, int b1 )
{
  return rgb2match( r1, g1, b1, 0, 255 );
}

int transMap::rgb2grey( int r1, int g1, int b1 )
{
  return rgb2match( r1, g1, b1, 232, 255 );
}

char * transMap::slot2xterm( int xterm, int bg )
{
    static char retval[16];

    if(xterm < 16) {
        sprintf(retval, "%s%s", bg ? "B_" : "", ansi_name[xterm]);
    } else if(xterm > 231) {
        if(xterm > 255) xterm = 255;
        sprintf(retval, "%s%02d", bg ? "BG" : "G", xterm-231);
    } else {
        xterm -= 16;
        sprintf(retval, "%s%d%d%d", bg ? "B" : "F", (xterm/36)%6, (xterm/6)%6, xterm%6);
    }

    return retval;
}


char * transMap::rgb2xterm256(const char * rgb, int bg)
{
    int red, green, blue;
    int slot;

    sscanf(rgb, "%02x%02x%02x", &red, &green, &blue);
    slot = rgb2xterm( red, green, blue );
    return slot2xterm(slot, bg);
}

const char * transMap::color_translate(const char *terminal, const char *symbol)
{
#ifdef TESTME
    printf("color_translate(%s, %s)\\n", terminal, symbol);
#endif
    if(trans_map.find(terminal) != trans_map.end())
    {
#ifdef TESTME
        printf("Found %s\\n", terminal);
#endif
        if(trans_map[terminal].find(symbol) != trans_map[terminal].end())
        {
#ifdef TESTME
            printf("Found %s\\n", symbol);
#endif
            return trans_map[terminal][symbol].c_str();
        }
    }

    if(strlen(symbol) == 7)
        if(symbol[0] == 'F' || symbol[0] == 'B')
            if( isxdigit(symbol[1]) &&
                isxdigit(symbol[2]) &&
                isxdigit(symbol[3]) &&
                isxdigit(symbol[4]) &&
                isxdigit(symbol[5]) &&
                isxdigit(symbol[6]) )
            {
                char *tmp = NULL;

                tmp = rgb2xterm256(symbol+1, (symbol[0] == 'B') ? 1 : 0);
#ifdef TESTME
                printf("Rendered %s\\n", tmp);
#endif
                if(trans_map[terminal].find(tmp) != trans_map[terminal].end())
                {
#ifdef TESTME
                    printf("Found %s\\n", tmp);
#endif
                    return trans_map[terminal][tmp].c_str();
                }
            }

#ifdef TESTME
    printf("NOT Found %s\\n", symbol);
#endif
    return symbol;
}

#ifdef TESTME
int main(int argc, char **argv)
{
    transMap mappy;
    size_t pos = 0;
    size_t epos = 0;

    std::string source = "Testing [G05]Grey [F034567]Stuff [OliveDrab]Forever [RESET]\\n";
    std::string result = "";
    printf("Source:  %s\\n", source.c_str());

    while( (pos = source.find("[", pos)) != std::string::npos )
    {
        printf("found [\\n");
        if( (epos = source.find("]", pos+1)) != std::string::npos )
        {
            std::string match;
            std::string repl;
            std::string reset;

            reset = mappy.color_translate("xterm-256color", "G05");
            reset = mappy.color_translate("xterm-256color", "RESET");
            printf("found ]\\n");
            match = source.substr(pos+1, epos-pos-1);
            printf("MATCH: \\"%s\\" - %d %d\\n", match.c_str(), match.length(), strlen(match.c_str()));
            printf("   at: %zd, %zd\\n", pos, epos);
            repl = mappy.color_translate("xterm-256color", match.c_str());
            printf("REPLACEMENT: \\"%s\\" - %d%s\\n", repl.c_str(), repl.length(), reset.c_str());

            source.replace(pos, epos-pos+1, repl);
            pos = epos = epos - (epos - pos) + repl.length();
            printf("Result:  %s\\n", source.c_str());
        }
    }
    printf("Result:  %s\\n", source.c_str());

    return 1;
}
#endif

EOM
    ;

    if(defined $file) {
        open FOO, ">$file" or die "Cannot open output $file";
        print FOO "$out\n";
        close FOO;
    } else {
        print "$out\n";
    }
}

sub output_c {
    my $file = shift;
    my $out = "";
    my $format_count = $FORMAT_LAST - $FORMAT_FIRST + 1;

    $out .= <<EOM
#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <ctype.h>
#include <string.h>

#define BUCKETS 100

struct s_stringMap;

typedef struct s_stringMap {
    const char *key;
    const char *value;
    struct s_stringMap *next;
} stringMap;

static stringMap trans_map[$format_count][BUCKETS];
static stringMap imc_pinkfish[BUCKETS];
static stringMap pinkfish_imc[BUCKETS];
static stringMap pinkfish_imc_cleanup[BUCKETS];
static stringMap extended_to_pinkfish[BUCKETS];

unsigned int term_name_lookup( const char *s );
unsigned int hashmap( const char *s );
void hash_init( stringMap *map );
void hash_add( stringMap *map, const char *k, const char *v );
const char * hash_find(stringMap *map, const char *k);

void transMap(void);
const char * color_translate( const char *terminal, const char *symbol );

void setupTransMap( void );
void setupImcPinkfishMap( void );
void setupPinkfishImcMap( void );
void setupPinkfishImcCleanupMap( void );
void setupExtendedToPinkfishMap( void );
int * xterm2rgb( int color );
float rgb_distance(int r1, int g1, int b1, int r2, int g2, int b2);
int rgb2match( int r1, int g1, int b1, int low, int high );
int rgb2ansi( int r1, int g1, int b1 );
int rgb2xterm( int r1, int g1, int b1 );
int rgb2grey( int r1, int g1, int b1 );
char * slot2xterm( int xterm, int bg );
char * rgb2xterm256(const char * rgb, int bg);

unsigned int term_name_lookup( const char *s )
{
EOM
    ;
    for( my $format = $FORMAT_FIRST; $format <= $FORMAT_LAST; $format++ ) {
        $out .= sprintf("    if(!strcmp(s, \"%s\")) return %d;\n", $term_name[$format], $format);
    }
    $out .= <<EOM
    return 0;
}

unsigned int hashmap( const char *s )
{
    unsigned int hash = 0;

    if(!s || !*s) return 0;
    do {
        hash += *s;
        hash *= 13;
        s++;
    } while (*s);

    return hash % BUCKETS;
}

void hash_init( stringMap *map )
{
    int i;

    for(i = 0; i < BUCKETS; i++)
    {
        map[i].key = NULL;
        map[i].value = NULL;
        map[i].next = NULL;
    }
}

void hash_add( stringMap *map, const char *k, const char *v )
{
    unsigned int hashcode;
    stringMap *p;

    hashcode = hashmap(k);
    p = &map[hashcode];
    while(p->key && strcmp(p->key, k) && p->next)
        p = p->next;

    if(!p->key) {
        /* First node? */
        p->key = (const char *)strdup(k);
        p->value = (const char *)strdup(v);
        p->next = NULL;
    } else if(!strcmp(p->key, k)) {
        /* Found our match! */
        if(p->value)
            free((void *)p->value);
        p->value = (const char *)strdup(v);
    } else {
        /* New key */
        p->next = (stringMap *)calloc(1, sizeof(stringMap));
        p = p->next;
        p->key = (const char *)strdup(k);
        p->value = (const char *)strdup(v);
        p->next = NULL;
    }
}

const char * hash_find(stringMap *map, const char *k)
{
    unsigned int hashcode;
    stringMap *p;

    hashcode = hashmap(k);
    p = &map[hashcode];
    while(p->key && strcmp(p->key, k) && p->next)
        p = p->next;

    if(!p->key)
        return NULL;

    if(!strcmp(p->key, k))
        return p->value;

    return NULL;
}

EOM
    ;

    {
        my %meta_trans = ();
        for( my $format = $FORMAT_FIRST; $format <= $FORMAT_LAST; $format++ ) {
            $meta_trans{$format} = ();
            $meta_trans{$format}{$_} = $attr_trans{$format}{$_} foreach (keys %{ $attr_trans{$format} });
            $meta_trans{$format}{$_} = $terminal_trans{$format}{$_} foreach (keys %{ $terminal_trans{$format} });
            $meta_trans{$format}{$_} = $ansi_trans{$format}{$_} foreach (keys %{ $ansi_trans{$format} });
            $meta_trans{$format}{$_} = $xterm_trans{$format}{$_} foreach (keys %{ $xterm_trans{$format} });
            $meta_trans{$format}{$_} = $x11_trans{$format}{$_} foreach (keys %{ $x11_trans{$format} });
        }

        my $format_count = $FORMAT_LAST - $FORMAT_FIRST + 1;
        $out .= "void setupTransMap( void ) {\n";
        $out .= "    unsigned int term_name;\n";

        for( my $format = $FORMAT_FIRST; $format <= $FORMAT_LAST; $format++ ) {
            $out .= "\n    term_name = term_name_lookup(\"$term_name[$format]\");\n";
            $out .= "    hash_init(trans_map[term_name]);\n";
            foreach my $k (sort keys %{ $meta_trans{$format} }) {
                my $v = $meta_trans{$format}{$k};
                $v =~ s/\"/\\\"/g;
                $v =~ s/\033/\\033/g;
                $out .= "    hash_add(trans_map[term_name], \"$k\", \"$v\");\n";
            }
        }
        $out .= "}\n\n";
    }

    {
        $out .= "void setupImcPinkfishMap( void ) {\n";
        {
            $out .= "    hash_init(imc_pinkfish);\n";
            foreach my $k (sort keys %imc_pinkfish) {
                my $v = $imc_pinkfish{$k};
                $v =~ s/\"/\\\"/g;
                $v =~ s/\033/\\033/g;
                $out .= "    hash_add(imc_pinkfish, \"$k\", \"$v\");\n";
            }
        }
        $out .= "}\n\n";
    }

    {
        my %meta = ();
        $meta{$_} = $pinkfish_imc{$_} foreach (keys %pinkfish_imc);
        $meta{$_} = $pinkfish_imc_ext{$_} foreach (keys %pinkfish_imc_ext);

        $out .= "void setupPinkfishImcMap( void ) {\n";
        {
            $out .= "    hash_init(pinkfish_imc);\n";
            foreach my $k (sort keys %meta) {
                my $v = $meta{$k};
                $v =~ s/\"/\\\"/g;
                $v =~ s/\033/\\033/g;
                $out .= "    hash_add(pinkfish_imc, \"$k\", \"$v\");\n";
            }
        }
        $out .= "}\n\n";
    }

    {
        $out .= "void setupPinkfishImcCleanupMap( void ) {\n";
        {
            $out .= "    hash_init(pinkfish_imc_cleanup);\n";
            foreach my $k (sort keys %pinkfish_imc_cleanup) {
                my $v = $pinkfish_imc_cleanup{$k};
                $v =~ s/\"/\\\"/g;
                $v =~ s/\033/\\033/g;
                $out .= "    hash_add(pinkfish_imc_cleanup, \"$k\", \"$v\");\n";
            }
        }
        $out .= "}\n\n";
    }

    {
        my %meta = ();
        for( my $i = 16; $i < 232; $i++ ) {
            my $token = int2base6($i);
            my $x = xterm2ansi($i);
            my $ansi = $ansi_name[$x];
            my $bansi = $ansi_b_name[$x];
            $meta{"%^F$token%^"} = "%^$ansi%^";
            $meta{"%^B$token%^"} = "%^$bansi%^";
        }
        {
            my $token = sprintf("%02d", 0);
            my $x = xterm2ansi(0);
            my $ansi = $ansi_name[$x];
            my $bansi = $ansi_b_name[$x];
            $meta{"%^G$token%^"} = "%^$ansi%^";
            $meta{"%^BG$token%^"} = "%^$bansi%^";
        }
        for( my $i = 232; $i < 256; $i++ ) {
            my $token = sprintf("%02d", $i - 232 + 1);
            my $x = xterm2ansi($i);
            my $ansi = $ansi_name[$x];
            my $bansi = $ansi_b_name[$x];
            $meta{"%^G$token%^"} = "%^$ansi%^";
            $meta{"%^BG$token%^"} = "%^$bansi%^";
        }
        {
            my $token = sprintf("%02d", 25);
            my $x = xterm2ansi(15);
            my $ansi = $ansi_name[$x];
            my $bansi = $ansi_b_name[$x];
            $meta{"%^G$token%^"} = "%^$ansi%^";
            $meta{"%^BG$token%^"} = "%^$bansi%^";
        }

        foreach my $k (sort keys %x11_values) {
            my ($r,$g,$b) = @{ $x11_values{$k} };
            my $ansi = $ansi_name[rgb2ansi($r, $g, $b)];
            $meta{"%^$k%^"} = "%^$ansi%^";
        }
        my $meta_count = scalar keys %meta;

        $out .= "void setupExtendedToPinkfishMap( void ) {\n";
        $out .= "    hash_init(extended_to_pinkfish);\n";
        foreach my $k (sort keys %meta) {
            my $v = $meta{$k};
            $out .= "    hash_add(extended_to_pinkfish, \"$k\", \"$v\");\n";
        }
        $out .= "}\n\n";
    }

    $out .= <<EOM
void transMap( void ) {
    setupTransMap();
    setupImcPinkfishMap();
    setupPinkfishImcMap();
    setupPinkfishImcCleanupMap();
    setupExtendedToPinkfishMap();
}

static const int hex_rgb[6] = { 0x00, 0x55, 0x88, 0xBB, 0xDD, 0xFF };

static const int ansi_rgb[16][3] = {
    { 0x00, 0x00, 0x00 }, /*  0  black */
    { 0xBB, 0x00, 0x00 }, /*  1  red */
    { 0x00, 0xBB, 0x00 }, /*  2  green */
    { 0xBB, 0xBB, 0x00 }, /*  3  yellow/orange */
    { 0x00, 0x00, 0xBB }, /*  4  blue */
    { 0xBB, 0x00, 0xBB }, /*  5  magenta */
    { 0x00, 0xBB, 0xBB }, /*  6  cyan */
    { 0xBB, 0xBB, 0xBB }, /*  7  light grey */

    { 0x55, 0x55, 0x55 }, /*  8  dark grey */
    { 0xFF, 0x55, 0x55 }, /*  9  bright red */
    { 0x55, 0xFF, 0x55 }, /* 10  bright green */
    { 0xFF, 0xFF, 0x55 }, /* 11  yellow */
    { 0x55, 0x55, 0xFF }, /* 12  bright blue */
    { 0xFF, 0x55, 0xFF }, /* 13  bright magenta */
    { 0x55, 0xFF, 0xFF }, /* 14  bright cyan */
    { 0xFF, 0xFF, 0xFF }  /* 15  white */
};

static const char * ansi_name[16] = {
    "BLACK", "RED", "GREEN", "ORANGE",
    "BLUE", "MAGENTA", "CYAN", "GREY",
    "DARKGREY", "LIGHTRED", "LIGHTGREEN", "YELLOW",
    "LIGHTBLUE", "PINK", "LIGHTCYAN", "WHITE"
};

static const int grey_rgb[24] = {
    0x08, 0x12, 0x1C, 0x26, 0x30, 0x3A, 
    0x44, 0x4E, 0x58, 0x62, 0x6C, 0x76, 
    0x80, 0x8A, 0x94, 0x9E, 0xA8, 0xB2, 
    0xBC, 0xC6, 0xD0, 0xDA, 0xE4, 0xEE
};

/*
 * This accepts an XTERM-256 slot number from 0 to 255,
 * and returns an array of RGB values.
 */
int * xterm2rgb( int color )
{
  int red, green, blue;
  static int retval[3];

  if( color < 16 ) {
        red = ansi_rgb[color][0];
        green = ansi_rgb[color][1];
        blue = ansi_rgb[color][2];
  } else if ( color < 232 ) {
        color -= 16;
        red = hex_rgb[(color/36)%6];
        green = hex_rgb[(color/6)%6];
        blue = hex_rgb[(color)%6];
  } else {
        color -= 232;
        red = green = blue = grey_rgb[color%24];
  }

  retval[0] = red;
  retval[1] = green;
  retval[2] = blue;
  return retval;
}

/*
 * This is a generic euclidean distance formula, used to determine
 * how "close" one set of RGB values is to another.  The weight
 * factors default to 1.0, but have suggested values based on the
 * human eye sensitivity, should you want to skew things for a
 * more artistic purpose.
 */
float rgb_distance(int r1, int g1, int b1, int r2, int g2, int b2)
{
  float rf, gf, bf;
  float dr, dg, db, dist;

  rf = 1.0; /* 0.241 */
  gf = 1.0; /* 0.691 */
  bf = 1.0; /* 0.068 */

  dr = abs(r2 - r1);
  dg = abs(g2 - g1);
  db = abs(b2 - b1);
  dist = sqrt((dr * dr * rf) + (dg * dg * gf) + (db * db * bf));

  return dist;
}

/*
 * This function finds the closest matching "xterm" colour
 * for the given 8-bit RGB values.  This is the workhorse
 * function which accepts ranges to allow specifying what
 * portion of the xterm colour pallete to use for matches.
 *
 * There are three API functions to find best match,
 * best ANSI match, and best greyscale match.
 *
 * Returns -1 on failure.
 */
int rgb2match( int r1, int g1, int b1, int low, int high )
{
  int i;
  int r2, g2, b2;
  int match;
  float max_distance, dist;
  int * tmp;

  match = -1;
  max_distance = 10000000000.0;

  for(i=low; i<=high; i++) {
    tmp = xterm2rgb(i);
    r2 = tmp[0];
    g2 = tmp[1];
    b2 = tmp[2];
    dist = rgb_distance(r1,g1,b1,r2,g2,b2);

    if(dist < max_distance) {
      max_distance = dist;
      match = i;
    }
  }

  return match;
}

/*
 * The following are just helper functions that
 * call rgb2match() with the correct parameters.
 */
int rgb2ansi( int r1, int g1, int b1 )
{
  return rgb2match( r1, g1, b1, 0, 15 );
}

int rgb2xterm( int r1, int g1, int b1 )
{
  return rgb2match( r1, g1, b1, 0, 255 );
}

int rgb2grey( int r1, int g1, int b1 )
{
  return rgb2match( r1, g1, b1, 232, 255 );
}

char * slot2xterm( int xterm, int bg )
{
    static char retval[16];

    if(xterm < 16) {
        sprintf(retval, "%s%s", bg ? "B_" : "", ansi_name[xterm]);
    } else if(xterm > 231) {
        if(xterm > 255) xterm = 255;
        sprintf(retval, "%s%02d", bg ? "BG" : "G", xterm-231);
    } else {
        xterm -= 16;
        sprintf(retval, "%s%d%d%d", bg ? "B" : "F", (xterm/36)%6, (xterm/6)%6, xterm%6);
    }

    return retval;
}


char * rgb2xterm256(const char * rgb, int bg)
{
    int red, green, blue;
    int slot;

    sscanf(rgb, "%02x%02x%02x", &red, &green, &blue);
    slot = rgb2xterm( red, green, blue );
    return slot2xterm(slot, bg);
}

const char * color_translate(const char *terminal, const char *symbol)
{
    unsigned int term = 0;
    const char *sym = NULL;

#ifdef TESTME
    printf("color_translate(%s, %s)\\n", terminal, symbol);
#endif
    term = term_name_lookup(terminal);

#ifdef TESTME
    printf("Found %s as %d\\n", terminal, term);
#endif
    if((sym = hash_find(trans_map[term], symbol)))
    {
#ifdef TESTME
        printf("Found %s\\n", symbol);
#endif
        return sym;
    }

    if(strlen(symbol) == 7)
        if(symbol[0] == 'F' || symbol[0] == 'B')
            if( isxdigit(symbol[1]) &&
                isxdigit(symbol[2]) &&
                isxdigit(symbol[3]) &&
                isxdigit(symbol[4]) &&
                isxdigit(symbol[5]) &&
                isxdigit(symbol[6]) )
            {
                char *tmp = NULL;

                tmp = rgb2xterm256(symbol+1, (symbol[0] == 'B') ? 1 : 0);
#ifdef TESTME
                printf("Rendered %s\\n", tmp);
#endif
                if((sym = hash_find(trans_map[term], tmp)))
                {
#ifdef TESTME
                    printf("Found %s\\n", tmp);
#endif
                    return sym;
                }
            }

#ifdef TESTME
    printf("NOT Found %s\\n", symbol);
#endif
    return symbol;
}

#ifdef TESTME
int main(int argc, char **argv)
{
    char result[8192] = "\\0";
    size_t opos = 0;
    size_t pos = 0;
    size_t epos = 0;
    char *sfind = NULL;
    char *efind = NULL;
    const char *reset = NULL;
    char *match = NULL;
    const char *repl = NULL;
    const char *source = "Testing [G05]Grey [F034567]Stuff [OliveDrab]Forever [RESET]\\n"; 

    transMap();

    printf("Source:  %s\\n", source);

    while( sfind = strstr(source+pos, "[") )
    {
        pos = sfind - source;
        strncat(result, source+opos, pos - opos);
        printf("found [\\n");
        if( efind = strstr(source+pos+1, "]") )
        {
            epos = efind - source;
            printf("found ]\\n");
            reset = color_translate("xterm-256color", "G05");
            reset = color_translate("xterm-256color", "RESET");
            match = calloc(epos-pos, sizeof(char));
            strncpy(match, source+pos+1, epos-pos-1);
            printf("MATCH: \\"%s\\" - %d\\n", match, strlen(match));
            printf("   at: %zd, %zd\\n", pos, epos);
            repl = color_translate("xterm-256color", match);
            printf("REPLACEMENT: \\"%s\\" - %d%s\\n", repl, strlen(repl), reset);
            strcat(result, repl);
            opos = pos = epos = epos+1;
            printf("Result:  %s\\n", result);
        }
    }
    strcat(result, source + opos);
    printf("Result:  %s\\n", result);

    return 1;
}
#endif

EOM
    ;

    if(defined $file) {
        open FOO, ">$file" or die "Cannot open output $file";
        print FOO "$out\n";
        close FOO;
    } else {
        print "$out\n";
    }
}

sub output_data {
    my $file = shift;
    my $out = "";
    my $format_count = $FORMAT_LAST - $FORMAT_FIRST + 1;

    $out .= "#TERMINALS\n";
    $out .= "$format_count";
    for( my $format = $FORMAT_FIRST; $format <= $FORMAT_LAST; $format++ ) {
        $out .= " $term_name[$format]";
    }
    $out .= "\n#END\n\n";

    {
        my %meta_trans = ();
        for( my $format = $FORMAT_FIRST; $format <= $FORMAT_LAST; $format++ ) {
            $meta_trans{$format} = ();
            $meta_trans{$format}{$_} = $attr_trans{$format}{$_} foreach (keys %{ $attr_trans{$format} });
            $meta_trans{$format}{$_} = $terminal_trans{$format}{$_} foreach (keys %{ $terminal_trans{$format} });
            $meta_trans{$format}{$_} = $ansi_trans{$format}{$_} foreach (keys %{ $ansi_trans{$format} });
            $meta_trans{$format}{$_} = $xterm_trans{$format}{$_} foreach (keys %{ $xterm_trans{$format} });
            $meta_trans{$format}{$_} = $x11_trans{$format}{$_} foreach (keys %{ $x11_trans{$format} });
        }
        my @meta_keys = ( sort keys %{ $meta_trans{ $FORMAT_FIRST } } );

        foreach my $k (@meta_keys) {
            $out .= "#TRANS\n";
            $out .= sprintf("%20s %s\n", "Name", $k);
            for( my $format = $FORMAT_FIRST; $format <= $FORMAT_LAST; $format++ ) {
                my $v = $meta_trans{$format}{$k};
                #$v =~ s/\"/\\\"/g;
                #$v =~ s/\033/\\033/g;
                $out .= sprintf("%20s %s\n", $term_name[$format], $v);
            }
            $out .= "#END\n\n";
        }
    }

    if(defined $file) {
        open FOO, ">$file" or die "Cannot open output $file";
        print FOO "$out\n";
        close FOO;
    } else {
        print "$out\n";
    }
}

setup_hex_arrays();
setup_colour_maps();
setup_xterm();
setup_x11();
setup_imc();

#print xterm_colours();
#print x11_colours();
output_dump('dump.txt');
output_lpc('pinkfish.h');
output_c('colormap.c');
output_data('colormap.dat');
output_cpp('mappings.cpp');
output_perl('terminal.pl');
chmod 0755, 'terminal.pl';
exit 1;

__DATA__
255 250 250		snow
248 248 255		ghost white
248 248 255		GhostWhite
245 245 245		white smoke
245 245 245		WhiteSmoke
220 220 220		gainsboro
255 250 240		floral white
255 250 240		FloralWhite
253 245 230		old lace
253 245 230		OldLace
250 240 230		linen
250 235 215		antique white
250 235 215		AntiqueWhite
255 239 213		papaya whip
255 239 213		PapayaWhip
255 235 205		blanched almond
255 235 205		BlanchedAlmond
255 228 196		bisque
255 218 185		peach puff
255 218 185		PeachPuff
255 222 173		navajo white
255 222 173		NavajoWhite
255 228 181		moccasin
255 248 220		cornsilk
255 255 240		ivory
255 250 205		lemon chiffon
255 250 205		LemonChiffon
255 245 238		seashell
240 255 240		honeydew
245 255 250		mint cream
245 255 250		MintCream
240 255 255		azure
240 248 255		alice blue
240 248 255		AliceBlue
230 230 250		lavender
255 240 245		lavender blush
255 240 245		LavenderBlush
255 228 225		misty rose
255 228 225		MistyRose
255 255 255		white
  0   0   0		black
 47  79  79		dark slate gray
 47  79  79		DarkSlateGray
 47  79  79		dark slate grey
 47  79  79		DarkSlateGrey
105 105 105		dim gray
105 105 105		DimGray
105 105 105		dim grey
105 105 105		DimGrey
112 128 144		slate gray
112 128 144		SlateGray
112 128 144		slate grey
112 128 144		SlateGrey
119 136 153		light slate gray
119 136 153		LightSlateGray
119 136 153		light slate grey
119 136 153		LightSlateGrey
190 190 190		gray
190 190 190		grey
211 211 211		light grey
211 211 211		LightGrey
211 211 211		light gray
211 211 211		LightGray
 25  25 112		midnight blue
 25  25 112		MidnightBlue
  0   0 128		navy
  0   0 128		navy blue
  0   0 128		NavyBlue
100 149 237		cornflower blue
100 149 237		CornflowerBlue
 72  61 139		dark slate blue
 72  61 139		DarkSlateBlue
106  90 205		slate blue
106  90 205		SlateBlue
123 104 238		medium slate blue
123 104 238		MediumSlateBlue
132 112 255		light slate blue
132 112 255		LightSlateBlue
  0   0 205		medium blue
  0   0 205		MediumBlue
 65 105 225		royal blue
 65 105 225		RoyalBlue
  0   0 255		blue
 30 144 255		dodger blue
 30 144 255		DodgerBlue
  0 191 255		deep sky blue
  0 191 255		DeepSkyBlue
135 206 235		sky blue
135 206 235		SkyBlue
135 206 250		light sky blue
135 206 250		LightSkyBlue
 70 130 180		steel blue
 70 130 180		SteelBlue
176 196 222		light steel blue
176 196 222		LightSteelBlue
173 216 230		light blue
173 216 230		LightBlue
176 224 230		powder blue
176 224 230		PowderBlue
175 238 238		pale turquoise
175 238 238		PaleTurquoise
  0 206 209		dark turquoise
  0 206 209		DarkTurquoise
 72 209 204		medium turquoise
 72 209 204		MediumTurquoise
 64 224 208		turquoise
  0 255 255		cyan
224 255 255		light cyan
224 255 255		LightCyan
 95 158 160		cadet blue
 95 158 160		CadetBlue
102 205 170		medium aquamarine
102 205 170		MediumAquamarine
127 255 212		aquamarine
  0 100   0		dark green
  0 100   0		DarkGreen
 85 107  47		dark olive green
 85 107  47		DarkOliveGreen
143 188 143		dark sea green
143 188 143		DarkSeaGreen
 46 139  87		sea green
 46 139  87		SeaGreen
 60 179 113		medium sea green
 60 179 113		MediumSeaGreen
 32 178 170		light sea green
 32 178 170		LightSeaGreen
152 251 152		pale green
152 251 152		PaleGreen
  0 255 127		spring green
  0 255 127		SpringGreen
124 252   0		lawn green
124 252   0		LawnGreen
  0 255   0		green
127 255   0		chartreuse
  0 250 154		medium spring green
  0 250 154		MediumSpringGreen
173 255  47		green yellow
173 255  47		GreenYellow
 50 205  50		lime green
 50 205  50		LimeGreen
154 205  50		yellow green
154 205  50		YellowGreen
 34 139  34		forest green
 34 139  34		ForestGreen
107 142  35		olive drab
107 142  35		OliveDrab
189 183 107		dark khaki
189 183 107		DarkKhaki
240 230 140		khaki
238 232 170		pale goldenrod
238 232 170		PaleGoldenrod
250 250 210		light goldenrod yellow
250 250 210		LightGoldenrodYellow
255 255 224		light yellow
255 255 224		LightYellow
255 255   0		yellow
255 215   0 		gold
238 221 130		light goldenrod
238 221 130		LightGoldenrod
218 165  32		goldenrod
184 134  11		dark goldenrod
184 134  11		DarkGoldenrod
188 143 143		rosy brown
188 143 143		RosyBrown
205  92  92		indian red
205  92  92		IndianRed
139  69  19		saddle brown
139  69  19		SaddleBrown
160  82  45		sienna
205 133  63		peru
222 184 135		burlywood
245 245 220		beige
245 222 179		wheat
244 164  96		sandy brown
244 164  96		SandyBrown
210 180 140		tan
210 105  30		chocolate
178  34  34		firebrick
165  42  42		brown
233 150 122		dark salmon
233 150 122		DarkSalmon
250 128 114		salmon
255 160 122		light salmon
255 160 122		LightSalmon
255 165   0		orange
255 140   0		dark orange
255 140   0		DarkOrange
255 127  80		coral
240 128 128		light coral
240 128 128		LightCoral
255  99  71		tomato
255  69   0		orange red
255  69   0		OrangeRed
255   0   0		red
255 105 180		hot pink
255 105 180		HotPink
255  20 147		deep pink
255  20 147		DeepPink
255 192 203		pink
255 182 193		light pink
255 182 193		LightPink
219 112 147		pale violet red
219 112 147		PaleVioletRed
176  48  96		maroon
199  21 133		medium violet red
199  21 133		MediumVioletRed
208  32 144		violet red
208  32 144		VioletRed
255   0 255		magenta
238 130 238		violet
221 160 221		plum
218 112 214		orchid
186  85 211		medium orchid
186  85 211		MediumOrchid
153  50 204		dark orchid
153  50 204		DarkOrchid
148   0 211		dark violet
148   0 211		DarkViolet
138  43 226		blue violet
138  43 226		BlueViolet
160  32 240		purple
147 112 219		medium purple
147 112 219		MediumPurple
216 191 216		thistle
255 250 250		snow1
238 233 233		snow2
205 201 201		snow3
139 137 137		snow4
255 245 238		seashell1
238 229 222		seashell2
205 197 191		seashell3
139 134 130		seashell4
255 239 219		AntiqueWhite1
238 223 204		AntiqueWhite2
205 192 176		AntiqueWhite3
139 131 120		AntiqueWhite4
255 228 196		bisque1
238 213 183		bisque2
205 183 158		bisque3
139 125 107		bisque4
255 218 185		PeachPuff1
238 203 173		PeachPuff2
205 175 149		PeachPuff3
139 119 101		PeachPuff4
255 222 173		NavajoWhite1
238 207 161		NavajoWhite2
205 179 139		NavajoWhite3
139 121	 94		NavajoWhite4
255 250 205		LemonChiffon1
238 233 191		LemonChiffon2
205 201 165		LemonChiffon3
139 137 112		LemonChiffon4
255 248 220		cornsilk1
238 232 205		cornsilk2
205 200 177		cornsilk3
139 136 120		cornsilk4
255 255 240		ivory1
238 238 224		ivory2
205 205 193		ivory3
139 139 131		ivory4
240 255 240		honeydew1
224 238 224		honeydew2
193 205 193		honeydew3
131 139 131		honeydew4
255 240 245		LavenderBlush1
238 224 229		LavenderBlush2
205 193 197		LavenderBlush3
139 131 134		LavenderBlush4
255 228 225		MistyRose1
238 213 210		MistyRose2
205 183 181		MistyRose3
139 125 123		MistyRose4
240 255 255		azure1
224 238 238		azure2
193 205 205		azure3
131 139 139		azure4
131 111 255		SlateBlue1
122 103 238		SlateBlue2
105  89 205		SlateBlue3
 71  60 139		SlateBlue4
 72 118 255		RoyalBlue1
 67 110 238		RoyalBlue2
 58  95 205		RoyalBlue3
 39  64 139		RoyalBlue4
  0   0 255		blue1
  0   0 238		blue2
  0   0 205		blue3
  0   0 139		blue4
 30 144 255		DodgerBlue1
 28 134 238		DodgerBlue2
 24 116 205		DodgerBlue3
 16  78 139		DodgerBlue4
 99 184 255		SteelBlue1
 92 172 238		SteelBlue2
 79 148 205		SteelBlue3
 54 100 139		SteelBlue4
  0 191 255		DeepSkyBlue1
  0 178 238		DeepSkyBlue2
  0 154 205		DeepSkyBlue3
  0 104 139		DeepSkyBlue4
135 206 255		SkyBlue1
126 192 238		SkyBlue2
108 166 205		SkyBlue3
 74 112 139		SkyBlue4
176 226 255		LightSkyBlue1
164 211 238		LightSkyBlue2
141 182 205		LightSkyBlue3
 96 123 139		LightSkyBlue4
198 226 255		SlateGray1
185 211 238		SlateGray2
159 182 205		SlateGray3
108 123 139		SlateGray4
202 225 255		LightSteelBlue1
188 210 238		LightSteelBlue2
162 181 205		LightSteelBlue3
110 123 139		LightSteelBlue4
191 239 255		LightBlue1
178 223 238		LightBlue2
154 192 205		LightBlue3
104 131 139		LightBlue4
224 255 255		LightCyan1
209 238 238		LightCyan2
180 205 205		LightCyan3
122 139 139		LightCyan4
187 255 255		PaleTurquoise1
174 238 238		PaleTurquoise2
150 205 205		PaleTurquoise3
102 139 139		PaleTurquoise4
152 245 255		CadetBlue1
142 229 238		CadetBlue2
122 197 205		CadetBlue3
 83 134 139		CadetBlue4
  0 245 255		turquoise1
  0 229 238		turquoise2
  0 197 205		turquoise3
  0 134 139		turquoise4
  0 255 255		cyan1
  0 238 238		cyan2
  0 205 205		cyan3
  0 139 139		cyan4
151 255 255		DarkSlateGray1
141 238 238		DarkSlateGray2
121 205 205		DarkSlateGray3
 82 139 139		DarkSlateGray4
127 255 212		aquamarine1
118 238 198		aquamarine2
102 205 170		aquamarine3
 69 139 116		aquamarine4
193 255 193		DarkSeaGreen1
180 238 180		DarkSeaGreen2
155 205 155		DarkSeaGreen3
105 139 105		DarkSeaGreen4
 84 255 159		SeaGreen1
 78 238 148		SeaGreen2
 67 205 128		SeaGreen3
 46 139	 87		SeaGreen4
154 255 154		PaleGreen1
144 238 144		PaleGreen2
124 205 124		PaleGreen3
 84 139	 84		PaleGreen4
  0 255 127		SpringGreen1
  0 238 118		SpringGreen2
  0 205 102		SpringGreen3
  0 139	 69		SpringGreen4
  0 255	  0		green1
  0 238	  0		green2
  0 205	  0		green3
  0 139	  0		green4
127 255	  0		chartreuse1
118 238	  0		chartreuse2
102 205	  0		chartreuse3
 69 139	  0		chartreuse4
192 255	 62		OliveDrab1
179 238	 58		OliveDrab2
154 205	 50		OliveDrab3
105 139	 34		OliveDrab4
202 255 112		DarkOliveGreen1
188 238 104		DarkOliveGreen2
162 205	 90		DarkOliveGreen3
110 139	 61		DarkOliveGreen4
255 246 143		khaki1
238 230 133		khaki2
205 198 115		khaki3
139 134	 78		khaki4
255 236 139		LightGoldenrod1
238 220 130		LightGoldenrod2
205 190 112		LightGoldenrod3
139 129	 76		LightGoldenrod4
255 255 224		LightYellow1
238 238 209		LightYellow2
205 205 180		LightYellow3
139 139 122		LightYellow4
255 255	  0		yellow1
238 238	  0		yellow2
205 205	  0		yellow3
139 139	  0		yellow4
255 215	  0		gold1
238 201	  0		gold2
205 173	  0		gold3
139 117	  0		gold4
255 193	 37		goldenrod1
238 180	 34		goldenrod2
205 155	 29		goldenrod3
139 105	 20		goldenrod4
255 185	 15		DarkGoldenrod1
238 173	 14		DarkGoldenrod2
205 149	 12		DarkGoldenrod3
139 101	  8		DarkGoldenrod4
255 193 193		RosyBrown1
238 180 180		RosyBrown2
205 155 155		RosyBrown3
139 105 105		RosyBrown4
255 106 106		IndianRed1
238  99	 99		IndianRed2
205  85	 85		IndianRed3
139  58	 58		IndianRed4
255 130	 71		sienna1
238 121	 66		sienna2
205 104	 57		sienna3
139  71	 38		sienna4
255 211 155		burlywood1
238 197 145		burlywood2
205 170 125		burlywood3
139 115	 85		burlywood4
255 231 186		wheat1
238 216 174		wheat2
205 186 150		wheat3
139 126 102		wheat4
255 165	 79		tan1
238 154	 73		tan2
205 133	 63		tan3
139  90	 43		tan4
255 127	 36		chocolate1
238 118	 33		chocolate2
205 102	 29		chocolate3
139  69	 19		chocolate4
255  48	 48		firebrick1
238  44	 44		firebrick2
205  38	 38		firebrick3
139  26	 26		firebrick4
255  64	 64		brown1
238  59	 59		brown2
205  51	 51		brown3
139  35	 35		brown4
255 140 105		salmon1
238 130	 98		salmon2
205 112	 84		salmon3
139  76	 57		salmon4
255 160 122		LightSalmon1
238 149 114		LightSalmon2
205 129	 98		LightSalmon3
139  87	 66		LightSalmon4
255 165	  0		orange1
238 154	  0		orange2
205 133	  0		orange3
139  90	  0		orange4
255 127	  0		DarkOrange1
238 118	  0		DarkOrange2
205 102	  0		DarkOrange3
139  69	  0		DarkOrange4
255 114	 86		coral1
238 106	 80		coral2
205  91	 69		coral3
139  62	 47		coral4
255  99	 71		tomato1
238  92	 66		tomato2
205  79	 57		tomato3
139  54	 38		tomato4
255  69	  0		OrangeRed1
238  64	  0		OrangeRed2
205  55	  0		OrangeRed3
139  37	  0		OrangeRed4
255   0	  0		red1
238   0	  0		red2
205   0	  0		red3
139   0	  0		red4
255  20 147		DeepPink1
238  18 137		DeepPink2
205  16 118		DeepPink3
139  10	 80		DeepPink4
255 110 180		HotPink1
238 106 167		HotPink2
205  96 144		HotPink3
139  58  98		HotPink4
255 181 197		pink1
238 169 184		pink2
205 145 158		pink3
139  99 108		pink4
255 174 185		LightPink1
238 162 173		LightPink2
205 140 149		LightPink3
139  95 101		LightPink4
255 130 171		PaleVioletRed1
238 121 159		PaleVioletRed2
205 104 137		PaleVioletRed3
139  71	 93		PaleVioletRed4
255  52 179		maroon1
238  48 167		maroon2
205  41 144		maroon3
139  28	 98		maroon4
255  62 150		VioletRed1
238  58 140		VioletRed2
205  50 120		VioletRed3
139  34	 82		VioletRed4
255   0 255		magenta1
238   0 238		magenta2
205   0 205		magenta3
139   0 139		magenta4
255 131 250		orchid1
238 122 233		orchid2
205 105 201		orchid3
139  71 137		orchid4
255 187 255		plum1
238 174 238		plum2
205 150 205		plum3
139 102 139		plum4
224 102 255		MediumOrchid1
209  95 238		MediumOrchid2
180  82 205		MediumOrchid3
122  55 139		MediumOrchid4
191  62 255		DarkOrchid1
178  58 238		DarkOrchid2
154  50 205		DarkOrchid3
104  34 139		DarkOrchid4
155  48 255		purple1
145  44 238		purple2
125  38 205		purple3
 85  26 139		purple4
171 130 255		MediumPurple1
159 121 238		MediumPurple2
137 104 205		MediumPurple3
 93  71 139		MediumPurple4
255 225 255		thistle1
238 210 238		thistle2
205 181 205		thistle3
139 123 139		thistle4
  0   0   0		gray0
  0   0   0		grey0
  3   3   3		gray1
  3   3   3		grey1
  5   5   5		gray2
  5   5   5		grey2
  8   8   8		gray3
  8   8   8		grey3
 10  10  10 		gray4
 10  10  10 		grey4
 13  13  13 		gray5
 13  13  13 		grey5
 15  15  15 		gray6
 15  15  15 		grey6
 18  18  18 		gray7
 18  18  18 		grey7
 20  20  20 		gray8
 20  20  20 		grey8
 23  23  23 		gray9
 23  23  23 		grey9
 26  26  26 		gray10
 26  26  26 		grey10
 28  28  28 		gray11
 28  28  28 		grey11
 31  31  31 		gray12
 31  31  31 		grey12
 33  33  33 		gray13
 33  33  33 		grey13
 36  36  36 		gray14
 36  36  36 		grey14
 38  38  38 		gray15
 38  38  38 		grey15
 41  41  41 		gray16
 41  41  41 		grey16
 43  43  43 		gray17
 43  43  43 		grey17
 46  46  46 		gray18
 46  46  46 		grey18
 48  48  48 		gray19
 48  48  48 		grey19
 51  51  51 		gray20
 51  51  51 		grey20
 54  54  54 		gray21
 54  54  54 		grey21
 56  56  56 		gray22
 56  56  56 		grey22
 59  59  59 		gray23
 59  59  59 		grey23
 61  61  61 		gray24
 61  61  61 		grey24
 64  64  64 		gray25
 64  64  64 		grey25
 66  66  66 		gray26
 66  66  66 		grey26
 69  69  69 		gray27
 69  69  69 		grey27
 71  71  71 		gray28
 71  71  71 		grey28
 74  74  74 		gray29
 74  74  74 		grey29
 77  77  77 		gray30
 77  77  77 		grey30
 79  79  79 		gray31
 79  79  79 		grey31
 82  82  82 		gray32
 82  82  82 		grey32
 84  84  84 		gray33
 84  84  84 		grey33
 87  87  87 		gray34
 87  87  87 		grey34
 89  89  89 		gray35
 89  89  89 		grey35
 92  92  92 		gray36
 92  92  92 		grey36
 94  94  94 		gray37
 94  94  94 		grey37
 97  97  97 		gray38
 97  97  97 		grey38
 99  99  99 		gray39
 99  99  99 		grey39
102 102 102 		gray40
102 102 102 		grey40
105 105 105 		gray41
105 105 105 		grey41
107 107 107 		gray42
107 107 107 		grey42
110 110 110 		gray43
110 110 110 		grey43
112 112 112 		gray44
112 112 112 		grey44
115 115 115 		gray45
115 115 115 		grey45
117 117 117 		gray46
117 117 117 		grey46
120 120 120 		gray47
120 120 120 		grey47
122 122 122 		gray48
122 122 122 		grey48
125 125 125 		gray49
125 125 125 		grey49
127 127 127 		gray50
127 127 127 		grey50
130 130 130 		gray51
130 130 130 		grey51
133 133 133 		gray52
133 133 133 		grey52
135 135 135 		gray53
135 135 135 		grey53
138 138 138 		gray54
138 138 138 		grey54
140 140 140 		gray55
140 140 140 		grey55
143 143 143 		gray56
143 143 143 		grey56
145 145 145 		gray57
145 145 145 		grey57
148 148 148 		gray58
148 148 148 		grey58
150 150 150 		gray59
150 150 150 		grey59
153 153 153 		gray60
153 153 153 		grey60
156 156 156 		gray61
156 156 156 		grey61
158 158 158 		gray62
158 158 158 		grey62
161 161 161 		gray63
161 161 161 		grey63
163 163 163 		gray64
163 163 163 		grey64
166 166 166 		gray65
166 166 166 		grey65
168 168 168 		gray66
168 168 168 		grey66
171 171 171 		gray67
171 171 171 		grey67
173 173 173 		gray68
173 173 173 		grey68
176 176 176 		gray69
176 176 176 		grey69
179 179 179 		gray70
179 179 179 		grey70
181 181 181 		gray71
181 181 181 		grey71
184 184 184 		gray72
184 184 184 		grey72
186 186 186 		gray73
186 186 186 		grey73
189 189 189 		gray74
189 189 189 		grey74
191 191 191 		gray75
191 191 191 		grey75
194 194 194 		gray76
194 194 194 		grey76
196 196 196 		gray77
196 196 196 		grey77
199 199 199 		gray78
199 199 199 		grey78
201 201 201 		gray79
201 201 201 		grey79
204 204 204 		gray80
204 204 204 		grey80
207 207 207 		gray81
207 207 207 		grey81
209 209 209 		gray82
209 209 209 		grey82
212 212 212 		gray83
212 212 212 		grey83
214 214 214 		gray84
214 214 214 		grey84
217 217 217 		gray85
217 217 217 		grey85
219 219 219 		gray86
219 219 219 		grey86
222 222 222 		gray87
222 222 222 		grey87
224 224 224 		gray88
224 224 224 		grey88
227 227 227 		gray89
227 227 227 		grey89
229 229 229 		gray90
229 229 229 		grey90
232 232 232 		gray91
232 232 232 		grey91
235 235 235 		gray92
235 235 235 		grey92
237 237 237 		gray93
237 237 237 		grey93
240 240 240 		gray94
240 240 240 		grey94
242 242 242 		gray95
242 242 242 		grey95
245 245 245 		gray96
245 245 245 		grey96
247 247 247 		gray97
247 247 247 		grey97
250 250 250 		gray98
250 250 250 		grey98
252 252 252 		gray99
252 252 252 		grey99
255 255 255 		gray100
255 255 255 		grey100
169 169 169		dark grey
169 169 169		DarkGrey
169 169 169		dark gray
169 169 169		DarkGray
0     0 139		dark blue
0     0 139		DarkBlue
0   139 139		dark cyan
0   139 139		DarkCyan
139   0 139		dark magenta
139   0 139		DarkMagenta
139   0   0		dark red
139   0   0		DarkRed
144 238 144		light green
144 238 144		LightGreen
