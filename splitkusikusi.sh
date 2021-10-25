sha=$(./splitsh-lite --prefix=kusikusi/)
git branch -f kusikusi $sha && git push kusikusi kusikusi:master
