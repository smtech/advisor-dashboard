# Advisor Dashboard LTI

This LTI is explicitly inspired by the University of Michigan presentation at InstructureCon 2015:

[![Using Canvas APIs to Serve a Campus Early Warning System](http://img.youtube.com/vi/uqJ2hwsB92M/0.jpg)](https://www.youtube.com/watch?v=uqJ2hwsB92M)

The reason that St. Mark's chose to use Canvas was to reduce the friction in communication about student progress and feedback, specifically with the trio of the student, the teacher and the student's advisor in mind. We want the advisor to be able to provide the student with the best possible counsel and advice, with minimal overhead to the teacher but _with_ high coordination with the teacher.

## The Setup

Each of our advisors (who are mostly teaching faculty) advise a group of 3-7 advisees. Each advisor has an advisory course in which the advisor is the teacher and the advisees are the students. These courses are created within our Advisory Groups sub-account.

This LTI is placed in the Advisory Groups sub-account, which causes it to:

  - Display an administrative dashboard to account administrators within the Advisory Groups sub-account. This provides a GUI for further configuration specific to our advisory setup (e.g. creating a matching observer user for each advisee)
  - Display a course-navigation entry to teachers of every course in the Advisory Groups sub-account.

## Course-level Advisor Dashboard

![Course-level Advisor Dashboard](/images/course-level-dashboard.png)

At the course level, advisors are given several options:

  - A "Relative Grades" view, that shows an advisee's performance in their classes relative to their classmates. This is drawn from the Analytics API, and normalizes all assignments to be presented as a percentage (without regard to total point value). Essentially, this is a variation of the Course Analytics view already available to teachers, but is (we think) a simpler, easier to "grok" presentation for advisors: is my advisee washed up on the beach, in the "river" with their peers or surfing the waves independently. ![Relative Grades](/images/relative-grades.png)
  - A listing of observer logins for their advisees. These observers are paired with the advisee via the User Observees API, which causes enrollment changes for the advisee to be synched with the observer (requiring no intervention from [our enrollment management script](https://github.com/smtech/canvas-blackbaud-enrollment-automation), which just handles student enrollments).
  - Quick access to the Faculty Journal for advisees, via our [Faculty Journal](https://github.com/smtech/canvas-faculty-journal) add-on, which allows teachers to browse through the faculty journal entries for entire classes a là SpeedGrader.

## Account-level Administrative Dashboard

![Account-level Administrative Dashboard](/images/account-level-dashboard.png)

At the account level, administrators are able to:

  - Create Advisor-Observers. This can be a time-intensive process (it involves a lot of API calls). It creates (or updates, if they already exist) the advisor-observer user for all students currently enrolled in advisory courses in the sub-account. These observers are paired via the User Observees API, so that subsequent enrollment changes for the student are mirrored for the observer, and the observer can log in and see most (but [not quite all](https://community.canvaslms.com/docs/DOC-2272)) of the student's interactions in Canvas.
  - Rename Advisory Groups. This may really be a one-off, but our advisory groups came out of our SIS this year with really dumb, non-transparent names. So this runs through the advisory group courses and names them with the teacher's last name: "Battis Advisory Group".
  - Download Observers CSV. Periodically, updates to the observer passwords fail. Not entirely clear why. This is a short-circuit around that problem: it generates a `users.csv` document with the stored passwords for all of the observers, for easy SIS CSV import. A really elegant developer would have just fed that straight into the API.

## Install

 The _best_ way to install this on your own LAMP server would be:

## Requirements

###### Composer

This LTI uses [Composer](http://getcomposer.org) to manage dependencies. Before starting, make sure that you have [installed the Composer command line tool](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)!

###### LAMP Stack

This is designed to run on a LAMP stack (Linux, Apache, MySQL, PHP). Presumably a savvy user could munge it around to work in some other configuration without too much sweat. But…

###### SSL Certificate (quasi-optional)

Additionally, all of the LTI interactions will run more smoothly if you are hosting this tool on a web server with valid SSL credentials. Happily, [Let's Encrypt](https://letsencrypt.org/) provides a free service and tool to acquire those credentials!

## Install

  1. Clone the repository (command line below, but you can use your favorite GUI GitHub client instead).
  ```BASH
  git clone https://github.com/smtech/advisor-dashboard.git
  ```
  2. Install dependencies (the `--prefer-dist` flag indicates that you want the distribution version of the dependency packages, excluding tests, examples, documentation, etc.)
  ```BASH
  cd advisor-dashboard
  composer install --prefer-dist
  ```
  3. Copy `config-example.xml` to `config.xml` and edit with your credentials.

  _You **must** include MySQL credentials, but you may choose between manually generating an API access token and providing your canvas instance `url` and the `token` **or** [acquiring your own Canvas Developer credentials](https://canvas.instructure.com/doc/api/file.oauth.html#oauth2-flow-0) and providing that `key` and `secret` to be used during installation to interactively request an API access token._

  4. If you've been doing all this on your web server, sweet! If not, upload the `advisor-dashboard` directory to your web server now.

  After uploading your files (or basking in the glow of not having to upload the files), double-check your file permissions to make sure that the web server user (usually `apache` or `www-data`) has read and write access to the the `advisor-dashboard` hierarchy.

  _Technically you only need **really** write access to the `/logs` directory and to `/vendor/battis/bootstrapsmarty/cache` and `/vendor/battis/bootstrapsmarty/templates_c` …but life is just simpler if you give write access to the whole directory._

  5. Point your browser at the install directory:

  ```
  https://your-domain.com/path/to/advisor-dashboard
  ```

  This will prompt the app to load the settings from `config.xml` into its cache and to configure itself to be ready to manage LTI Tool Consumers. You will be redirected to the consumer management page `admin/` (after interactively providing an API access token if `config.xml` has a `key` and `secret`).

  6. Create a Tool Consumer. The name is human-readable and just needs to mean something to you. The key and secret are automagically generated, but you can change them to whatever you want.

  7. Create an app placement in Canvas in your account of advisory courses. Use the "by URL" app configuration using the URL provided on the consumers page (and the key and secret for the consumer you just created).


  You're all set!

## Planned Improvements

In general, improvements to our tools come through direct feedback from our faculty and students. We track specific requests (and bugs) using [the GitHub issue tracker for this repository](https://github.com/smtech/advisor-dashboard/issues).
