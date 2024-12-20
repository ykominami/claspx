# frozen_string_literal: true

require "debug"
require 'loggerx'
require 'pathname'
require 'open3'

module Claspx
  LOG_DIR = "log"
  SRC_DIR = "src"
  DIST_DIR = "dist"
  WORK_DIR = "work"
  WORK_STORE_DIR = "work_store"
  CLASP_JSON = ".clasp.json"
  GITIGNORE_FILE = ".gitignore"
  GITI = ".gitignore"
  PACKAGE_JSON = "package.json"
  APPSSCRIPT_JSON = "appsscript.json"
  ETC_DIR = "etc"
  CLASPX_DIR = "test_data/clasp"
  TOP_DIR_PN = Pathname.new(__FILE__).join("../..")  
  LOG_DIR_PN = TOP_DIR_PN.join(LOG_DIR)
  MAX_PROJECTS = 300
end

module Claspx
  class Loggerxcm < Loggerx::Loggerxcm
    # Loggerxcm.init("claspxC_", :default, LOG_DIR_PN, false, level: :debug)
    # Loggerxcm.init("claspxC_", :default, LOG_DIR_PN, false, level: :info)
    # Loggerxcm.init("claspxC_", :default, LOG_DIR_PN, true, level: :info) 
    Loggerxcm.init("claspxC_", :default, LOG_DIR_PN, true, level: :debug)
  end

  class Loggerx < Loggerx::Loggerx
    # @loggerx = Loggerx.new("claspxI_", :default, LOG_DIR_PN, false, level: :debug)
    @loggerx = Loggerx.new("claspxI_", :default, LOG_DIR_PN, false, level: :info)

    def self.loggerx
      @loggerx
    end
  end

  class Error < StandardError; end
  # Your code goes here...
end

require_relative "claspx/util"
require_relative "claspx/cmd"
require_relative "claspx/ghcmd"
require_relative "claspx/gitcmd"
require_relative "claspx/setup"
require_relative "claspx/result"

require_relative "claspx/setupclasp"
require_relative "claspx/setupgit"
require_relative "claspx/setupgithub"

require_relative "claspx/env"
require_relative "claspx/jsscript"

require_relative "claspx/project"
require_relative "claspx/projectgroup"
require_relative "claspx/app"


require_relative "claspx/version"
require_relative "claspx/util2"
require_relative "claspx/cmdxcmd"
require_relative "claspx/claspcmd"
require_relative "claspx/whichcmd"

# Claspx::Project.new
