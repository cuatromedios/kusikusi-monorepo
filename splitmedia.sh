sha=$(./splitsh-lite --prefix=packages/media/)
git branch -f media $sha && git push media media:master