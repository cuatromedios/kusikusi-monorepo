sha=$(./splitsh-lite --prefix=packages/admin/)
git branch -f admin $sha && git push admin admin:master
