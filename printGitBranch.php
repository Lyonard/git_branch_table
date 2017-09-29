<?php
/**
 * Created by PhpStorm.
 * User: roberto
 * Date: 26/01/16
 * Time: 16.23
 */

$tabulationCols = 30;

$branchList = `git branch -a`;
$branchList = array_filter( explode( "\n", $branchList ) );

$colors   = new Colors();
$fgColors = $colors->getForegroundColors();

$remotes      = array();
$branches     = array();
$branch2Color = array();
foreach ( $branchList as $row ) {
    $row = trim( $row, "\t\n\r\0\x0B* " );
    if ( strpos( $row, "/" ) === false ) {
        //local
        $remote             = "local";
        $branches[ $row ][] = "local";
    } else {
        $row                     = explode( "/", $row );

	if( $row[ 2 ] == "HEAD -> origin"){
		continue;
	}

        $remote                  = $row[ 1 ];
        $branches[ $row[ 2 ] ][] = $row[ 1 ];
    }

    if ( !in_array( $remote, $remotes ) ) {
        $remotes[] = $remote;

        if ( $remote == 'origin' ) {
            $branch2Color[ $remote ] = $fgColors[ 0 ]; //red
            unset( $fgColors[ 0 ] );
        } else {
            $branch2Color[ $remote ] = array_pop( $fgColors );
        }
    }
}


$num_remotes = count($remotes); 
foreach ( $remotes as $remote ) {
    $string = $colors->getColoredString(
                    str_pad( $remote, $tabulationCols, " ", STR_PAD_RIGHT ),
                    $branch2Color[ $remote ]
            ) . "| ";
    print_r( $string );
}

$string = str_pad( "", $num_remotes * ($tabulationCols + 1)  , "=", STR_PAD_RIGHT );
echo "\n" . $string . "\n\n";

foreach ( $branches as $bName => $branch ) {
    $outRow = array();
    foreach ( $remotes as $remote ) {
        if ( !in_array( $remote, $branch ) ) {
            $outRow = "";
        } else {
            $outRow = $bName;
        }
        $string = $colors->getColoredString(
                        str_pad( $outRow, $tabulationCols, " ", STR_PAD_RIGHT ),
                        $branch2Color[ $remote ]
                ) . "| ";
        print_r( $string );

    }
    echo "\n";
    $string = str_pad( "", $num_remotes * ($tabulationCols + 1) , "-", STR_PAD_RIGHT ) . "| ";
    print_r( $string );

    echo "\n";
}

class Colors {
    private $foreground_colors = array();
    private $background_colors = array();

    public function __construct() {
        // Set up shell colors
        $this->foreground_colors[ 'red' ]          = '0;31';
        $this->foreground_colors[ 'green' ]        = '0;32';
        $this->foreground_colors[ 'light_green' ]  = '1;32';
        $this->foreground_colors[ 'light_cyan' ]   = '1;36';
        $this->foreground_colors[ 'light_purple' ] = '1;35';
        $this->foreground_colors[ 'light_gray' ]   = '0;37';
        $this->foreground_colors[ 'yellow' ]       = '1;33';
        $this->foreground_colors[ 'cyan' ]         = '0;36';
        $this->foreground_colors[ 'light_blue' ]   = '1;34';
        $this->foreground_colors[ 'white' ]        = '1;37';

//        $this->background_colors['black'] = '40';
//        $this->background_colors['red'] = '41';
//        $this->background_colors['green'] = '42';
//        $this->background_colors['yellow'] = '43';
//        $this->background_colors['blue'] = '44';
//        $this->background_colors['magenta'] = '45';
//        $this->background_colors['cyan'] = '46';
//        $this->background_colors['light_gray'] = '47';
    }

    // Returns colored string
    public function getColoredString( $string, $foreground_color = null, $background_color = null ) {
        $colored_string = "";

        // Check if given foreground color found
        if ( isset( $this->foreground_colors[ $foreground_color ] ) ) {
            $colored_string .= "\033[" . $this->foreground_colors[ $foreground_color ] . "m";
        }
        // Check if given background color found
        if ( isset( $this->background_colors[ $background_color ] ) ) {
            $colored_string .= "\033[" . $this->background_colors[ $background_color ] . "m";
        }

        // Add string and end coloring
        $colored_string .= $string . "\033[0m";

        return $colored_string;
    }

    // Returns all foreground color names
    public function getForegroundColors() {
        return array_keys( $this->foreground_colors );
    }

    // Returns all background color names
    public function getBackgroundColors() {
        return array_keys( $this->background_colors );
    }
}

?>

