<?php

namespace App\Util\Lexer;

class RestrictedNames {

  static $blacklist = [
     "about",
     "abuse",
     "administrator",
     "app",
     "autoconfig",
     "blog",
     "broadcasthost",
     "community",
     "contact",
     "contact-us",
     "contact_us",
     "copyright",
     "css",
     "d",
     "dashboard",
     "dev",
     "developer",
     "developers",
     "discover",
     "discovers",
     "doc",
     "docs",
     "download",
     "domainadmin",
     "domainadministrator",
     "email",
     "errors",
     "events",
     "example",
     "faq",
     "faqs",
     "features",
     "ftp",
     "guest",
     "guests",
     "help",
     "hostmaster",
     "hostmaster",
     "image",
     "images",
     "imap",
     "img",
     "info",
     "info",
     "is",
     "isatap",
     "it",
     "js",
     "localdomain",
     "localhost",
     "mail",
     "mailer-daemon",
     "mailerdaemon",
     "marketing",
     "me",
     "media",
     "mis",
     "mx",
     "new",
     "news",
     "news",
     "no-reply",
     "nobody",
     "noc",
     "noreply",
     "ns0",
     "ns1",
     "ns2",
     "ns3",
     "ns4",
     "ns5",
     "ns6",
     "ns7",
     "ns8",
     "ns9",
     "owner",
     "pop",
     "pop3",
     "postmaster",
     "pricing",
     "privacy",
     "root",
     "sales",
     "security",
     "signin",
     "signout",
     "smtp",
     "src",
     "ssladmin",
     "ssladministrator",
     "sslwebmaster",
     "status",
     "support",
     "support",
     "sys",
     "sysadmin",
     "system",
     "terms",
     "tutorial",
     "tutorials",
     "usenet",
     "uucp",
     "webmaster",
     "wpad",
     "www"
   ];

  static $reserved = [
     // Reserved for instance admin
     "admin",

     // Static Assets
     "assets",

     // Laravel Horizon
     "horizon",

     // Reserved routes
     "account",
     "api",
     "auth",
     "i",
     "discover",
     "home",
     "login",
     "logout",
     "p",
     "password",
     "search",
     "settings",
     "site",
     "timeline",
     "user",
     "users",
  ];

  public static function get()
  {
     
     $reserved = $blacklist = [];

     if(true == config('pixelfed.restricted_names.use_blacklist')) {
          $blacklist = self::$blacklist;
     }

     if(true == config('pixelfed.restricted_names.reserved_routes')) {
          $reserved = self::$reserved;
     }
     return array_merge($blacklist, $reserved);
  }

}