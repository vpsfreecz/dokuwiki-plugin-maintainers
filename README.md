Maintainers
===========

This plugin defines tags to specify maintainers for wiki pages and show
information about maintainers. On the wiki, the list of maintainers is just
informational, it does not have a deeper meaning.

However, the generated HTML tags can be easily parsed by 3rd party
applications. For example, we're using it to ping responsible maintainers
on IRC using an IRC bot that checks the wiki for changed pages.

Usage
=====
If a page should have maintainers, specify them using tag `<maintainers>`:

    <maintainers>
    nick1
    nick2
    </maintainers>

The plugin transforms this to an unordered list, where each name is a link to
page `$user_ns/$nick`. `$user_ns` is a namespace where each maintainer has his
page, it defaults to `users` and can be changed in the config.

Maintainer's profile is defined using tag `<maintainer>`:

    <maintainer name="My Full Name">
    irc  nick1
    mail nick1@example.com
    </maintainer>

    Some other text may follow.

Both tags generate HTML tags that are meant to be parsed by external
applications.
