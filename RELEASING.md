# Release checklist

* [ ] Get master to the appropriate code release state.
      [GitHub Actions](https://github.com/digiaonline/lumen-contentful-sync/actions)
      should be running cleanly for all merges to master.
      [![GitHub Actions status](https://github.com/digiaonline/lumen-contentful-sync/workflows/Test/badge.svg)](https://github.com/digiaonline/lumen-contentful-sync/actions)

* [ ] Update the CHANGELOG:

```bash
git checkout master
edit CHANGELOG.md
git add CHANGELOG.md
git commit -m "Update the CHANGELOG"
git push
```

* [ ] Tag with next version:

```bash
git tag 3.4.0
git push --tags
```

* [ ] After a minute or two, check the new version is at
      https://packagist.org/packages/digiaonline/lumen-contentful-sync
