mkdir gh-pages
cd gh-pages

# Set identity
git config --global user.email "travis@travis-ci.org"
git config --global user.name "Travis"

# Add branch
git init
git remote add origin https://${GH_TOKEN}@github.com/skimia/assets.git > /dev/null
git checkout -B gh-pages
git pull origin gh-pages

cd ..

curl -OS http://couscous.io/couscous.phar

php couscous.phar generate --target gh-pages

# Get ApiGen.phar
wget http://www.apigen.org/apigen.phar

# Generate Api
php apigen.phar generate -s src -d gh-pages/api

cd gh-pages
# Push generated files
git add --all .
git commit -m "API updated"
git push origin gh-pages -fq > /dev/null
