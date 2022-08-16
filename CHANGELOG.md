# Changelog

## [Unreleased](https://github.com/bastien70/dbsaver/tree/HEAD)

[Full Changelog](https://github.com/bastien70/dbsaver/compare/2.0.0...HEAD)

**Implemented enhancements:**

- \[Feature\] Add backups options support [\#4](https://github.com/bastien70/dbsaver/issues/4)

## [2.0.0](https://github.com/bastien70/dbsaver/tree/2.0.0) (2022-08-15)

[Full Changelog](https://github.com/bastien70/dbsaver/compare/1.2.0...2.0.0)

**Implemented enhancements:**

- Change admin/user privileges [\#64](https://github.com/bastien70/dbsaver/issues/64)
- Set upload configuration from dashboard [\#58](https://github.com/bastien70/dbsaver/issues/58)
- Upgrade to PHP 8.1 [\#53](https://github.com/bastien70/dbsaver/issues/53)
- Add command to switch storage provider [\#51](https://github.com/bastien70/dbsaver/issues/51)
- Upgrade to Symfony 6 [\#50](https://github.com/bastien70/dbsaver/issues/50)
- Added backup custom periodicity support [\#73](https://github.com/bastien70/dbsaver/pull/73) ([bastien70](https://github.com/bastien70))
- Given the same permissions between users and admins [\#72](https://github.com/bastien70/dbsaver/pull/72) ([bastien70](https://github.com/bastien70))
- Added backup options when creating/updating database [\#70](https://github.com/bastien70/dbsaver/pull/70) ([bastien70](https://github.com/bastien70))
- Major changes : User can now configure storage spaces from Dashboard [\#62](https://github.com/bastien70/dbsaver/pull/62) ([bastien70](https://github.com/bastien70))
- Upgrade Symfony to 6.1 [\#55](https://github.com/bastien70/dbsaver/pull/55) ([jmsche](https://github.com/jmsche))

**Fixed bugs:**

- \[Bug\] Max backups option does not remove oldest backup but newest [\#63](https://github.com/bastien70/dbsaver/issues/63)

**Closed issues:**

- Drop French docs [\#69](https://github.com/bastien70/dbsaver/issues/69)
- \[Feature\] Add an option for backups periodicity [\#68](https://github.com/bastien70/dbsaver/issues/68)
- \[Feature\] Replace Gaufrette with Flysystem [\#61](https://github.com/bastien70/dbsaver/issues/61)
- \[Feature\] Add messenger for creating backups [\#60](https://github.com/bastien70/dbsaver/issues/60)

**Merged pull requests:**

- Removed french documentation [\#71](https://github.com/bastien70/dbsaver/pull/71) ([bastien70](https://github.com/bastien70))
- Updated welcome page [\#67](https://github.com/bastien70/dbsaver/pull/67) ([bastien70](https://github.com/bastien70))
- Added new query and criteria for correct removing of older backups [\#66](https://github.com/bastien70/dbsaver/pull/66) ([ToshY](https://github.com/ToshY))
- Misc improvements [\#57](https://github.com/bastien70/dbsaver/pull/57) ([jmsche](https://github.com/jmsche))
- Switch from Alice to Foundry [\#56](https://github.com/bastien70/dbsaver/pull/56) ([jmsche](https://github.com/jmsche))
- Upgrade Symfony to 6.0.6 & other vendors [\#54](https://github.com/bastien70/dbsaver/pull/54) ([jmsche](https://github.com/jmsche))
- Upgrade vendors [\#49](https://github.com/bastien70/dbsaver/pull/49) ([jmsche](https://github.com/jmsche))
- Increase phpstan level to 6 [\#48](https://github.com/bastien70/dbsaver/pull/48) ([jmsche](https://github.com/jmsche))
- Use DBAL types instead of strings [\#47](https://github.com/bastien70/dbsaver/pull/47) ([jmsche](https://github.com/jmsche))
- Upgrade vendors [\#46](https://github.com/bastien70/dbsaver/pull/46) ([jmsche](https://github.com/jmsche))
- Use EasyAdmin layout for Forgot Password feature, add remember me checkbox [\#45](https://github.com/bastien70/dbsaver/pull/45) ([jmsche](https://github.com/jmsche))
- Allow dev version of laminas/laminas-code to allow PHP 8.1 [\#44](https://github.com/bastien70/dbsaver/pull/44) ([jmsche](https://github.com/jmsche))
- Test against PHP 8.1 [\#43](https://github.com/bastien70/dbsaver/pull/43) ([jmsche](https://github.com/jmsche))
- Upgrade Symfony to 5.3.9 & other vendors, sync recipes [\#42](https://github.com/bastien70/dbsaver/pull/42) ([jmsche](https://github.com/jmsche))
- Update hautelook/alice-bundle as its Github repo changed [\#41](https://github.com/bastien70/dbsaver/pull/41) ([jmsche](https://github.com/jmsche))
- Allow choosing app environment during post-install [\#40](https://github.com/bastien70/dbsaver/pull/40) ([jmsche](https://github.com/jmsche))
- Allow user to receive or not automatic emails [\#39](https://github.com/bastien70/dbsaver/pull/39) ([jmsche](https://github.com/jmsche))

## [1.2.0](https://github.com/bastien70/dbsaver/tree/1.2.0) (2021-09-06)

[Full Changelog](https://github.com/bastien70/dbsaver/compare/1.1.0...1.2.0)

**Closed issues:**

- Allow to manually remove a backup [\#32](https://github.com/bastien70/dbsaver/issues/32)

**Merged pull requests:**

- Cleanup vendors [\#38](https://github.com/bastien70/dbsaver/pull/38) ([jmsche](https://github.com/jmsche))
- Add docs about updating the application [\#37](https://github.com/bastien70/dbsaver/pull/37) ([jmsche](https://github.com/jmsche))
- Added reset password support [\#36](https://github.com/bastien70/dbsaver/pull/36) ([bastien70](https://github.com/bastien70))
- Allow to manually remove a backup [\#35](https://github.com/bastien70/dbsaver/pull/35) ([jmsche](https://github.com/jmsche))
- Send email in user's locale [\#34](https://github.com/bastien70/dbsaver/pull/34) ([jmsche](https://github.com/jmsche))
- PostInstall command: add an option to only ask for missing params [\#33](https://github.com/bastien70/dbsaver/pull/33) ([jmsche](https://github.com/jmsche))
- Docs refactor [\#31](https://github.com/bastien70/dbsaver/pull/31) ([jmsche](https://github.com/jmsche))
- User: add locale & allow them to control their settings [\#30](https://github.com/bastien70/dbsaver/pull/30) ([jmsche](https://github.com/jmsche))
- Remove unused constant [\#29](https://github.com/bastien70/dbsaver/pull/29) ([jmsche](https://github.com/jmsche))
- Notification email: customize title & footer, use Mailer [\#28](https://github.com/bastien70/dbsaver/pull/28) ([jmsche](https://github.com/jmsche))
- Post install command: offer existing values when running command again [\#27](https://github.com/bastien70/dbsaver/pull/27) ([jmsche](https://github.com/jmsche))
- Add tests for DatabaseValidator & don't apply it if database name is … [\#26](https://github.com/bastien70/dbsaver/pull/26) ([jmsche](https://github.com/jmsche))
- Send notification emails after backups [\#25](https://github.com/bastien70/dbsaver/pull/25) ([jmsche](https://github.com/jmsche))
- Improvement of the CRUD Database index [\#24](https://github.com/bastien70/dbsaver/pull/24) ([bastien70](https://github.com/bastien70))
- Use Docker for local tests [\#23](https://github.com/bastien70/dbsaver/pull/23) ([jmsche](https://github.com/jmsche))
- Change main path from "/dbsaver" to "/" [\#22](https://github.com/bastien70/dbsaver/pull/22) ([jmsche](https://github.com/jmsche))
- Database connection status [\#21](https://github.com/bastien70/dbsaver/pull/21) ([jmsche](https://github.com/jmsche))
- Test entities & improve Backup controller tests [\#20](https://github.com/bastien70/dbsaver/pull/20) ([jmsche](https://github.com/jmsche))
- Fix license in composer.json [\#19](https://github.com/bastien70/dbsaver/pull/19) ([jmsche](https://github.com/jmsche))
- Improve forms [\#18](https://github.com/bastien70/dbsaver/pull/18) ([jmsche](https://github.com/jmsche))
- Allow to test database connection [\#17](https://github.com/bastien70/dbsaver/pull/17) ([jmsche](https://github.com/jmsche))
- Make tests use .env.test.local instead of .env.local [\#16](https://github.com/bastien70/dbsaver/pull/16) ([jmsche](https://github.com/jmsche))

## [1.1.0](https://github.com/bastien70/dbsaver/tree/1.1.0) (2021-08-24)

[Full Changelog](https://github.com/bastien70/dbsaver/compare/1.0.0...1.1.0)

**Implemented enhancements:**

- Add badges on README [\#8](https://github.com/bastien70/dbsaver/pull/8) ([jmsche](https://github.com/jmsche))

**Merged pull requests:**

- Added cascade remove to databases user [\#15](https://github.com/bastien70/dbsaver/pull/15) ([bastien70](https://github.com/bastien70))
- Improvements [\#14](https://github.com/bastien70/dbsaver/pull/14) ([jmsche](https://github.com/jmsche))
- Added User CRUD [\#13](https://github.com/bastien70/dbsaver/pull/13) ([bastien70](https://github.com/bastien70))
- Entities: rename some properties & add a trait [\#12](https://github.com/bastien70/dbsaver/pull/12) ([jmsche](https://github.com/jmsche))
- Post install command [\#11](https://github.com/bastien70/dbsaver/pull/11) ([jmsche](https://github.com/jmsche))
- Fix README [\#10](https://github.com/bastien70/dbsaver/pull/10) ([jmsche](https://github.com/jmsche))
- Translations & locale switcher [\#9](https://github.com/bastien70/dbsaver/pull/9) ([jmsche](https://github.com/jmsche))
- Setup PHPUnit for Github Actions [\#7](https://github.com/bastien70/dbsaver/pull/7) ([jmsche](https://github.com/jmsche))
- Add PHP CS Fixer to Github Actions [\#6](https://github.com/bastien70/dbsaver/pull/6) ([jmsche](https://github.com/jmsche))
- One role per user, improve make-user command and ask for role [\#5](https://github.com/bastien70/dbsaver/pull/5) ([jmsche](https://github.com/jmsche))
- Flash messages [\#3](https://github.com/bastien70/dbsaver/pull/3) ([jmsche](https://github.com/jmsche))
- Add install & update in Taskfile [\#2](https://github.com/bastien70/dbsaver/pull/2) ([jmsche](https://github.com/jmsche))
- Refactor [\#1](https://github.com/bastien70/dbsaver/pull/1) ([jmsche](https://github.com/jmsche))

## [1.0.0](https://github.com/bastien70/dbsaver/tree/1.0.0) (2021-08-18)

[Full Changelog](https://github.com/bastien70/dbsaver/compare/f41655ea80f7d9e13ac48ea055402c12d78ca3ab...1.0.0)



\* *This Changelog was automatically generated by [github_changelog_generator](https://github.com/github-changelog-generator/github-changelog-generator)*
