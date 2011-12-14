<?php
/*
 * This file is part of the {{ }} package.
 *
 * (c) Yo-An Lin <cornelius.howl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace tests\GetOptionKit;
use GetOptionKit\ContinuousOptionParser;
use GetOptionKit\OptionSpecCollection;

class ContinuousOptionParserTest extends \PHPUnit_Framework_TestCase 
{

    function testParser() 
    {
        $specs = new OptionSpecCollection;
        $spec_verbose = $specs->add('v|verbose');
        $spec_color = $specs->add('c|color');
        $spec_debug = $specs->add('d|debug');

        ok( $spec_verbose );
        ok( $spec_color );
        ok( $spec_debug );
        ok( $specs );
        // $parser = new ContinuousOptionParser();
    }


    /* test parser without options */
    function testParser2()
    {
        $appspecs = new OptionSpecCollection;
        $appspecs->add('v|verbose');
        $appspecs->add('c|color');
        $appspecs->add('d|debug');

        $cmdspecs = new OptionSpecCollection;
        $cmdspecs->add('a');
        $cmdspecs->add('b');
        $cmdspecs->add('c');


        $parser = new ContinuousOptionParser( $appspecs );
        ok( $parser );

        $subcommands = array('subcommand1','subcommand2','subcommand3');
        $subcommand_specs = array(
            'subcommand1' => $cmdspecs,
            'subcommand2' => $cmdspecs,
            'subcommand3' => $cmdspecs,
        );
        $subcommand_options = array();

        $argv = explode(' ','program subcommand1 subcommand2 subcommand3 arg1 arg2 arg3');
        $app_options = $parser->parse( $argv );
        $arguments = array();
        while( ! $parser->isEnd() ) {
            if( @$subcommands[0] && $parser->getCurrentArgument() == $subcommands[0] ) {
                $parser->advance();
                $subcommand = array_shift( $subcommands );
                $parser->setSpecs( $subcommand_specs[$subcommand] );
                $subcommand_options[ $subcommand ] = $parser->continueParse();
            } else {
                $arguments[] = $parser->advance();
            }
        }

        is( 'arg1', $arguments[0] );
        is( 'arg2', $arguments[1] );
        is( 'arg3', $arguments[2] );
        ok( $subcommand_options );
        ok( $subcommand_options['subcommand1'] );
        ok( $subcommand_options['subcommand2'] );
        ok( $subcommand_options['subcommand3'] );
    }

    function testParser3()
    {
        $appspecs = new OptionSpecCollection;
        $appspecs->add('v|verbose');
        $appspecs->add('c|color');
        $appspecs->add('d|debug');

        $cmdspecs = new OptionSpecCollection;
        $cmdspecs->add('a');
        $cmdspecs->add('b');
        $cmdspecs->add('c');



        $subcommands = array('subcommand1','subcommand2','subcommand3');
        $subcommand_specs = array(
            'subcommand1' => $cmdspecs,
            'subcommand2' => $cmdspecs,
            'subcommand3' => $cmdspecs,
        );
        $subcommand_options = array();
        $arguments = array();

        $argv = explode(' ','program -v -d -c subcommand1 -a -b -c subcommand2 -c subcommand3 arg1 arg2 arg3');
        $parser = new ContinuousOptionParser( $appspecs );
        ok( $parser );
        $app_options = $parser->parse( $argv );
        while( ! $parser->isEnd() ) {
            if( @$subcommands[0] && $parser->getCurrentArgument() == $subcommands[0] ) {
                $parser->advance();
                $subcommand = array_shift( $subcommands );
                $parser->setSpecs( $subcommand_specs[$subcommand] );
                $subcommand_options[ $subcommand ] = $parser->continueParse();
            } else {
                $arguments[] = $parser->advance();
            }
        }

        count_ok( 3, $arguments );
        is( 'arg1', $arguments[0] );
        is( 'arg2', $arguments[1] );
        is( 'arg3', $arguments[2] );
        ok( $subcommand_options );
        ok( $subcommand_options['subcommand1'] );
        ok( $subcommand_options['subcommand1']->a );
        ok( $subcommand_options['subcommand1']->b );
        ok( $subcommand_options['subcommand1']->c );

        ok( $subcommand_options['subcommand2'] );
        ok( ! $subcommand_options['subcommand2']->a );
        ok( ! $subcommand_options['subcommand2']->b );
        ok( $subcommand_options['subcommand2']->c );
        ok( $subcommand_options['subcommand3'] );
    }
}

