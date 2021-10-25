sha=$(./splitsh-lite --prefix=packages/forms/)
git branch -f forms $sha && git push forms forms:master
