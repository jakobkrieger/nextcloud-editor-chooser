# Nextcloud Editor Chooser - Build & Release
# Usage:
#   make build      - Install deps + build frontend
#   make release    - Create a release tarball/zip ready for Nextcloud
#   make clean      - Remove build artifacts

app_name=editorchooser
build_dir=$(CURDIR)/build
release_dir=$(build_dir)/release
sign_dir=$(build_dir)/sign
package_name=$(app_name)
version=$(shell grep '<version>' appinfo/info.xml | sed 's/.*<version>\(.*\)<\/version>.*/\1/')

# Files/dirs to include in the release package
release_files=appinfo \
              img \
              js \
              l10n \
              lib \
              templates \
              COPYING \
              LICENSE \
              README.md

.PHONY: all
all: build

# Install npm dependencies and build frontend
.PHONY: build
build: npm-install npm-build

.PHONY: npm-install
npm-install:
	npm ci

.PHONY: npm-build
npm-build:
	npm run build

# Clean all build artifacts
.PHONY: clean
clean:
	rm -rf $(build_dir)
	rm -rf js/
	rm -rf node_modules/

# Create release package (tar.gz and zip)
.PHONY: release
release: clean build package

.PHONY: package
package:
	@echo "Packaging $(app_name) v$(version)..."
	mkdir -p $(release_dir)/$(app_name)
	@# Copy only the files needed for production
	@for f in $(release_files); do \
		if [ -e "$$f" ]; then \
			cp -a "$$f" $(release_dir)/$(app_name)/; \
		fi; \
	done
	@# Remove source maps from js/ in the package
	find $(release_dir)/$(app_name)/js -name '*.map' -delete 2>/dev/null || true
	@# Create tar.gz
	cd $(release_dir) && tar -czf $(CURDIR)/build/$(app_name)-$(version).tar.gz $(app_name)
	@# Create zip
	cd $(release_dir) && zip -rq $(CURDIR)/build/$(app_name)-$(version).zip $(app_name)
	@echo ""
	@echo "Release artifacts:"
	@echo "  build/$(app_name)-$(version).tar.gz"
	@echo "  build/$(app_name)-$(version).zip"
	@echo ""
	@echo "To install: extract into your Nextcloud 'apps/' directory"
	@echo "  e.g.: cd /var/www/nextcloud/apps && tar xzf $(app_name)-$(version).tar.gz"

# Development build (faster, with source maps)
.PHONY: dev
dev: npm-install
	npm run dev

.PHONY: watch
watch: npm-install
	npm run watch
