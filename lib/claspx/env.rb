require 'dotenv/load'

module Claspx
  class Env
  
    CLASPX_DIR_PN = Pathname.new(CLASPX_DIR)
    CLASPX_ETC_DIR_PN = CLASPX_DIR_PN.join(ETC_DIR)
    CLASPX_ETC_DIST_DIR_PN = CLASPX_ETC_DIR_PN.join(DIST_DIR)
    TEMPLATE_CLASP_JSON_PN = CLASPX_ETC_DIR_PN.join(CLASP_JSON)
    TEMPLATE_PACKAGE_JSON_PN = CLASPX_ETC_DIR_PN.join(PACKAGE_JSON)
    TEMPLATE_APPSSCRIPT_JSON_PN = CLASPX_ETC_DIST_DIR_PN.join(APPSSCRIPT_JSON)
    TEMPLATE_GITIGNORE_PN = CLASPX_ETC_DIR_PN.join(GITI)
    GOOGLE_API_KEY = ENV['GOOGLE_API_KEY']
    Loggerxcm.info "GOOGLE_API_KEY: #{GOOGLE_API_KEY}"

    class << self
      def TEMPLATE_CLASP_JSON_PN
        TEMPLATE_CLASP_JSON_PN
      end

      def TEMPLATE_PACKAGE_JSON_PN
        TEMPLATE_PACKAGE_JSON_PN
      end

      def TEMPLATE_APPSSCRIPT_JSON_PN
        TEMPLATE_APPSSCRIPT_JSON_PN
      end

      def TEMPLATE_GITIGNORE_PN
        TEMPLATE_GITIGNORE_PN
      end
    end
  end
end
