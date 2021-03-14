sha=$(./splitsh-lite --prefix=packages/models/)
git branch -f models $sha && git push models models:master