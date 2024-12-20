require "json"
require "open3"
require "pathname"
require "ykxutils"
require "claspx/project_group"
require "forwardable"

module Claspx
  class App
    extend Forwardable

    attr_reader :project, :project_group
    def initialize(top_dir)
      @top_dir_pn = Pathname.new(top_dir)
    end

    def setup_project_group(remote_hash)
      @project_group = ProjectGroup.new(@top_dir_pn, remote_hash)
    end

    def delegators :@project_group, :setup_all, :setup_one, :clasp_clone_all,
      :clasp_pull_all, :clasp_push_all, :transform_js_script_all, 
      :transformed_js_list_all,
      :make_import_export_file_all, :file_list, :reform_js_script_all
    end
  end
end
