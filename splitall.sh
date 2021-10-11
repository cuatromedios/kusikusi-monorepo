sha=$(./splitsh-lite --prefix=packages/models/)
git branch -f models $sha && git push models models:master

sha=$(./splitsh-lite --prefix=packages/media/)
git branch -f media $sha && git push media media:master

sha=$(./splitsh-lite --prefix=packages/website/)
git branch -f website $sha && git push website website:master

sha=$(./splitsh-lite --prefix=packages/admin/)
git branch -f admin $sha && git push admin admin:master
