<?php
/**
 * StatusNet, the distributed open-source microblogging tool
 *
 * Plugin to show questions when a user registers
 *
 * PHP version 5
 *
 * LICENCE: This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Plugin
 * @package   StatusNet
 * @author    Stanislav Nikitin <pztrn@pztrn.name>
 * @copyright 2013
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link      http://status.net/
 */

if (!defined('STATUSNET') && !defined('LACONICA')) {
    exit(1);
}

class QuestionCaptchaPlugin extends Plugin
{
    var $display_errors;
    var $failed;
    var $ssl;

    function onInitializePlugin()
    {
        // There is nothing to do yet.
    }
    
    function onEndRegistrationFormData($action)
    {
        $action->elementStart('li');
        // TRANS: Field label.
        $action->raw('<label for="questioncaptcha">'._m('Captcha').'</label>');

        $action->element('div', array('id' => 'questioncaptcha'));
        
        // The text.
        // Getting a random code from config.
        $totalcodes = count($this->codes);
        $randomcode = rand(0, $totalcodes);
        $codestring = $this->codes[$randomcode];
        $codes = explode(",", $codestring);
        $codesstring = "{$codes[0]}, {$codes[1]}, {$codes[2]}";
        // Get array of symbols positions we want user to enter.
        // We require 3 symbols of each code.
        $symbols = array(
                         0 => rand(1, strlen($codes[0])),
                         1 => rand(1, strlen($codes[0])),
                         2 => rand(1, strlen($codes[0])),
                         );
                         
        $symbolsstring = "{$symbols[0]}, {$symbols[1]} and {$symbols[2]}";
        
        // Prepare string for output.
        $captchastring = "We got these codes: {$codesstring}. Enter {$symbolsstring} symbols of each code.";
        
        $action->element('input', array('name' => 'questionanswer'));
        // Hidden thing. Insecure. But good than nothing :).
        $eleclass = "{$randomcode}_{$symbols[0]}_{$symbols[1]}_{$symbols[2]}";
        $action->element('input', array('name' => 'thasec', 'style' => 'display:none;', 'value' => $eleclass));
        
        $action->element('p', array('class' => 'form_guide'), $captchastring);
        $action->elementEnd('li');
        return true;
    }

    function onStartRegistrationTry($action)
    {
        $sec = $_POST['thasec'];
        $data = explode("_", $sec);
        $input = $_POST['questionanswer'];
        // Getting our codes
        $codeidx = $data[0];
        $codes = $this->codes[$codeidx];
        $codes = explode(",", $codes);
        
        // Composing a string for validation.
        $validstring = "";
        foreach ($codes as $i => $value)
        {
            $validstring = $validstring . $value[$data[1] - 1];
            $validstring = $validstring . $value[$data[2] - 1];
            $validstring = $validstring . $value[$data[3] - 1];
        }
        
        // DEBUG ONLY!
        //file_put_contents("file/sec.txt", "{$sec} : ${input} : {$validstring}\n\n", FILE_APPEND);

        if (!($validstring == $input)) {
            // TRANS: Error message displayed if a provided captcha response does not match.
            $action->showForm(_m('Captcha does not match!'));
            return false;
        }
    }

    function onPluginVersion(&$versions)
    {
        $versions[] = array('name' => 'QuestionCaptcha',
                            'version' => '0.1',
                            'author' => 'Stanislav Nikitin',
                            'homepage' => 'http://wiki.loadaverage.org/statusnet/plugins/questioncaptcha',
                            'rawdescription' => 'Adds questions while someone registers.',
                            );
        return true;
    }
}
