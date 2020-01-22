<?php

/*
* Check Email string for validity
* Paul Gregg <pgregg@pgregg.com>
* January 2002, Updated Nov 2003, Logic fix May 2006
* Updated May 2008 - rewrote localpart/domainpart splitting
* http://www.pgregg.com/projects/php/code/validate_email.inc.phps
* Copyright 2002-2008, Paul Gregg.
*
* Open Source Code:   If you use this code on your site for public
* access (i.e. on the Internet) then you must attribute the author and
* source web site: http://pgregg.com/projects/php/code/validate_email.inc.phps
*
* Email Validation (syntax/format only) is frequently a hotly discussed
* topic on forums, irc channels and blogs.  Some people believe that to be
* "correct" your pattern should be RFC 822/2822 complient, others fall into
* the take anything camp.
* Personally, I believe a more liberal approach is necessary - the spirit of
* RFCs is "Be strict in what you send and liberal in what you receive".
* Many email addresses might not be strictly to RFC pattern spec, but they
* work just fine, and in these days of multibyte characters and "foreign"
* character sets, the picture becomes a lot more complicated.
*
*/

class ValidateEmail {

  public static function valid($emailstr) {

    // Make the email address lower case and remove whitespace
    $emailstr = strtolower(trim($emailstr));

    // Split it up into before and after the @ symbol
    $email_components = explode('@', $emailstr);

    // Everything after the last @ is the domain, before is the local part
    $domain_part = array_pop($email_components);
    $local_part = implode('@', $email_components);

    // sanitize quoted parts
    $local_part = preg_replace('/\\\./', '_', $local_part);
    $local_part = preg_replace('/"[^"]+"/', '.', $local_part);

    // comments ( this is a comment ) are permitted in domain parts
    $domain_part = preg_replace('/\([^()]*\)/', '', $domain_part);

    // Make sure there are no more @ (we sanitized valid ones above)
    if (strpos($local_part, '@')!==false)
        return FALSE;

    // Check that the username is >= 1 char
    if (strlen($local_part) == 0)
        return FALSE;

    // Split the domain part into the dotted parts
    $domain_components = explode('.', $domain_part);

    // check there are at least 2
    if (count($domain_components) < 2)
        return FALSE;

    // Check each domain part to ensure it doesn't start or end with a bad char
    foreach ($domain_components as $domain_component)
      if ( strlen($domain_component) > 0 ) {
        if ( preg_match('/[\.-]/', $domain_component[0])
          || preg_match('/[\.-]/', $domain_component[strlen($domain_component)-1]) )
          return FALSE;
      } else
        return FALSE;


    // Check the last domain component has 2-6 chars (.uk to .museum)
    $domain_last = array_pop($domain_components);
    if (strlen($domain_last) < 2 || strlen($domain_last) > 6)
        return FALSE;

    // Check for valid chars - Domains can only have A-Z, 0-9, ., and the - chars,
    // or be in the form [123.123.123.123]
    if ( preg_match('/^\[(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\]$/', $domain_part, $ipnum) )
        return (ip2long($ipnum[1]) === false ? false : true);

    if ( preg_match('/^[a-z0-9\.-]+$/', $domain_part) )
        return TRUE;

    // If we get here then it didn't pass
    return FALSE;


  }

}
