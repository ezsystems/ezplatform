FROM solr:6-alpine

# Copy solr config from the version used by eZ Platform
COPY vendor/ezsystems/ezplatform-solr-search-engine/lib/Resources/config/solr/ /opt/solr/server/tmp

# Prepare config
RUN mkdir -p /opt/solr/server/ez/template \
 && cp -R /opt/solr/server/tmp/* /opt/solr/server/ez/template \
 && cp /opt/solr/server/solr/configsets/basic_configs/conf/currency.xml /opt/solr/server/ez/template \
 && cp /opt/solr/server/solr/configsets/basic_configs/conf/solrconfig.xml /opt/solr/server/ez/template \
 && cp /opt/solr/server/solr/configsets/basic_configs/conf/stopwords.txt /opt/solr/server/ez/template \
 && cp /opt/solr/server/solr/configsets/basic_configs/conf/synonyms.txt /opt/solr/server/ez/template \
 && cp /opt/solr/server/solr/configsets/basic_configs/conf/elevate.xml /opt/solr/server/ez/template \
 && cp /opt/solr/server/solr/solr.xml /opt/solr/server/ez \
 && sed -i.bak '/<updateRequestProcessorChain name="add-unknown-fields-to-the-schema">/,/<\/updateRequestProcessorChain>/d' /opt/solr/server/ez/template/solrconfig.xml \
 && sed -ie 's/${solr.autoSoftCommit.maxTime:-1}/${solr.autoSoftCommit.maxTime:20}/' /opt/solr/server/ez/template/solrconfig.xml

# Set our core config as home
ENV SOLR_HOME /opt/solr/server/ez

# Make sure core is created on startup
CMD ["solr-create", "-c", "collection1", "-d", "/opt/solr/server/ez/template"]
