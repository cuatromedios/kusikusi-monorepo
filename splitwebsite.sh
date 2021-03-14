sha=$(./splitsh-lite --prefix=packages/website/)
git branch -f website $sha && git push website website:master
