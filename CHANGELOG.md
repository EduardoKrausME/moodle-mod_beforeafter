# Changelog

This changelog documents only the two most recent published GitHub releases. Each entry describes the verified changes between its release tag and the immediately preceding **published** release. Changes made later on `master` are excluded.

**Release history note:** A `V 1.0.4` commit is included in the comparison between `1.0.3` and `1.0.5`, but no published GitHub Release for `1.0.4` was found. This changelog does not treat that commit as a separately published release.

## 1.0.5 - 2026-10-15

### Changed

- Updated the GitHub Actions CI PostgreSQL service from version 16 to 17.
- Updated the MariaDB CI service from version 10 to 11.4, including its startup environment variables and health-check configuration.
- Added the plugin icon image at `pix/icon.png` and reorganized the SVG logo asset as `pix/monologo.svg`.
- Updated the plugin release identifier from `1.0.3` to `1.0.5` and its build number from `2026101200` to `2026101501` across the two published release tags. An intermediate `V 1.0.4` commit falls within this interval, without a corresponding published release.

No functional changes to the Before After activity were identified between the published `1.0.3` and `1.0.5` tags.

[Compare 1.0.3...1.0.5](https://github.com/EduardoKrausME/moodle-mod_beforeafter/compare/1.0.3...1.0.5)

## 1.0.3 - 2026-10-12

### Changed

- Updated the PHP 8.3 CI matrix entry from `MOODLE_502_STABLE` (Moodle 5.2) to `MOODLE_503_STABLE` (Moodle 5.3), while retaining the Moodle 4.5 entry.
- Hardened the GitHub release workflow against shell injection by passing the commit message through an environment variable rather than directly interpolating it into a shell command.
- Updated the plugin release identifier from `1.0.2` to `1.0.3` and its build number from `2026100300` to `2026101200`.

No functional changes to the Before After activity were identified between the published `1.0.2` and `1.0.3` tags.

[Compare 1.0.2...1.0.3](https://github.com/EduardoKrausME/moodle-mod_beforeafter/compare/1.0.2...1.0.3)
