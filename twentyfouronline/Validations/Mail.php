<?php

/**
 * Mail.php
 *
 * -Description-
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @link       https://www.twentyfouronline.org
 *
 * @copyright  2017 Tony Murray
 * @author     Tony Murray <murraytony@gmail.com>
 */

namespace twentyfouronline\Validations;

use App\Facades\twentyfouronlineConfig;
use twentyfouronline\Validator;

class Mail extends BaseValidation
{
    protected static $RUN_BY_DEFAULT = false;

    /**
     * Validate this module.
     * To return ValidationResults, call ok, warn, fail, or result methods on the $validator
     *
     * @param  Validator  $validator
     */
    public function validate(Validator $validator): void
    {
        if (twentyfouronlineConfig::get('alert.transports.mail') === true) {
            $run_test = 1;
            if (! twentyfouronlineConfig::has('alert.default_mail')) {
                $validator->fail('default_mail config option needs to be specified to test email');
                $run_test = 0;
            } elseif (twentyfouronlineConfig::get('email_backend') == 'sendmail') {
                if (! twentyfouronlineConfig::has('email_sendmail_path')) {
                    $validator->fail('You have selected sendmail but not configured email_sendmail_path');
                    $run_test = 0;
                } elseif (! file_exists(twentyfouronlineConfig::get('email_sendmail_path'))) {
                    $validator->fail('The configured email_sendmail_path is not valid');
                    $run_test = 0;
                }
            } elseif (twentyfouronlineConfig::get('email_backend') == 'smtp') {
                if (! twentyfouronlineConfig::has('email_smtp_host')) {
                    $validator->fail('You have selected SMTP but not configured an SMTP host');
                    $run_test = 0;
                }
                if (! twentyfouronlineConfig::has('email_smtp_port')) {
                    $validator->fail('You have selected SMTP but not configured an SMTP port');
                    $run_test = 0;
                }
                if (twentyfouronlineConfig::get('email_smtp_auth')
                    && (! twentyfouronlineConfig::has('email_smtp_username') || ! twentyfouronlineConfig::has('email_smtp_password'))
                ) {
                    $validator->fail('You have selected SMTP auth but have not configured both username and password');
                    $run_test = 0;
                }
            }//end if
            if ($run_test == 1) {
                $email = twentyfouronlineConfig::get('alert.default_mail');
                try {
                    \twentyfouronline\Util\Mail::send($email, 'Test email', 'Testing email from NMS');
                    $validator->ok('Email has been sent');
                } catch (\Exception $e) {
                    $validator->fail("Issue sending email to $email with error " . $e->getMessage());
                }
            }
        }//end if
    }
}




