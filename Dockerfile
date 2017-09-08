FROM ezsystems/php:7.0-v1 as builder

# This is prod image (for dev use just mount your application as host volume into php image we extend here)
ENV SYMFONY_ENV=prod

# Copy in project files into work dir
COPY . /var/www

# Check for ignored folders to avoid layer issues, ref: https://github.com/docker/docker/issues/783
RUN if [ -d .git ]; then echo "ERROR: .dockerignore folders detected, exiting" && exit 1; fi

# Install and prepare install
RUN mkdir -p web/var
# For now, only run composer in order to generate parameters.yml
RUN composer run-script post-install-cmd --no-interaction
RUN composer dump-autoload --optimize

# Next, remove everything we don't want to be copied to next build stage
# Clear cache again so env variables are taken into account on startup
RUN rm -Rf app/logs/* app/cache/*/*
# Looks like we need to keep web/bundles ( like web/bundles/ezstudioui/js/views/ezs-landingpageview.js ) or else
# urls like http://localhost:8080/_ezcombo?/bundles/ezstudioui/js/views/ezs-landingpageview.js&/tpl/handlebars/studiolandingpageconfigview-ez-template.js&/bundles/ezstudioui/js/views/ezs-landingpageconfigview.js&/tpl/handlebars/studiolayoutselectorview-ez-template.js&/bundles/ezstudioui/js/views/ezs-layoutselectorview.js&/tpl/handlebars/studiolandingpageconfigpopupformview-ez-template.js&/bundles/ezstudioui/js/views/forms/ezs-landingpageconfigpopupformview.js&/tpl/handlebars/landingpagecreatorview-ez-template.js&/bundles/ezsystemsformbuilder/js/models/fb-formfield-model.js&/bundles/ezsystemsformbuilder/js/lists/fb-formfields-modellist.js&/bundles/ezsystemsformbuilder/js/models/fb-formpage-model.js&/bundles/ezsystemsformbuilder/js/lists/fb-formpages-modellist.js&/bundles/ezsystemsformbuilder/js/models/fb-form-model.js&/tpl/handlebars/fbbasetabview-ez-template.js&/bundles/ezsystemsformbuilder/js/tabs/fb-base-tabview.js&/tpl/handlebars/fbpanelview-ez-template.js&/bundles/ezsystemsformbuilder/js/panels/fb-panelview.js
# will not work when loading http://localhost:8080/ez
RUN rm -rf web/css web/fonts web/js web/var


FROM ezsystems/php:7.1-v1

# This is prod image (for dev use just mount your application as host volume into php image we extend here)
ENV SYMFONY_ENV=prod

COPY --from=builder /var/www /var/www

# Fix permissions for www-data
RUN chown -R www-data:www-data app/cache app/logs \
    && find app/cache app/logs -type d -print0 | xargs -0 chmod -R 775 \
    && find app/cache app/logs -type f -print0 | xargs -0 chmod -R 664
