module Claspx
  class Project
    attr_accessor :name, :path, :git_repo, :url, :github_url_ssh,
                  :github_url_https, :idx, :from_remote, :from_local,
                  :git_cmd, :src_dir, :work_dir, :work_store_dir,
                  :dist_dir, :jsscripts

    REMOTE = 0
    LOCAL = 1
    def initialize(name, project_dir_pn)
      # 0: retmote 1: local
      @from = [false, false]
      @name = name
      @path = project_dir_pn
      @src_dir_pn = @path.join(SRC_DIR)
      @src_dir_pn.mkpath
      @dist_dir_pn = @path.join(DIST_DIR)
      @dist_dir_pn.mkpath
      @work_dir_pn = @path.join(WORK_DIR)
      @work_dir_pn.mkpath
      @work_store_dir_pn = @path.join(WORK_STORE_DIR)
      @work_store_dir_pn.mkpath

      @git_repo = nil
      @url = nil
      @github_url_ssh = nil
      @github_url_https = nil
      @idx = nil
      @from_remote = nil
      @from_local = nil
      @git_cmd = nil

      @jsscripts = {}
    end

    def is_remote
      @from[REMOTE]
    end

    def is_local
      @from[LOCAL]
    end

    def set_remote
      @from[REMOTE] = true
    end

    def set_local
      @from[LOCAL] = true
    end

    def is_all
      @from[REMOTE] && @from[LOCAL]
    end

    def is_local_only
      !@from[REMOTE] && @from[LOCAL]
    end

    def is_remote_only
      @from[REMOTE] && !@from[LOCAL]
    end

    def is_none
      !@from[REMOTE] && !@from[LOCAL]
    end

    def valid_git_repo?
      !@git_repo.nil? && @git_repo.valid_repo?
    end

    def get_src_child_list
      @src_dir_pn.children
    end

    def get_dist_child_list
      @dist_dir_pn.children
    end

    def setup
      all_counter = 0
      error_counter = 0
      error_array = []

      setup_clasp = SetupClasp.new(self, Env.TEMPLATE_CLASP_JSON_PN, Env.TEMPLATE_PACKAGE_JSON_PN,
                                   Env.TEMPLATE_APPSSCRIPT_JSON_PN)
      ret = setup_clasp.execute

      all_counter += 1
      if ret.error?
        error_counter += 1
        error_array.push([ret2.message, self])
      end

      setup_git = SetupGit.new(self, Env.TEMPLATE_GITIGNORE_PN)
      ret2 = setup_git.execute
      all_counter += 1
      if ret2.error?
        error_counter += 1
        error_array.push([ret2.message, self])
      end

      setup_github = SetupGithub.new(self)
      ret3 = setup_github.execute
      all_counter += 1
      if ret3.error?
        error_counter += 1
        error_array.push([ret3.message, self])
      end

      status = true
      status = false if error_counter > 0
      ret = Result.new(status,
                       "Claspx#setup|all_counter=#{all_counter}|error_counter=#{error_counter}|project=#{@name}")
      ret.add_array(error_array)
    end

    def get_parnt_dir_and_basename(src_file)
      src_filename = src_file.basename
      dest_file = @dist_dir_pn.join(src_filename)
      basename = src_file.basename(".*")
      parent_dir_of_dest_file = @work_store_dir_pn.join(basename)
      parent_dir_of_dest_file.mkpath

      [dest_file, basename, parent_dir_of_dest_file]
    end

    def register_jsscript(src_file, basename)
      jsscript = Jsscript.new(src_file)

      basename_str = basename.to_s
      @jsscripts[basename_str] = jsscript
    end

    def make_jsscript(srcfile)
      dest_file, basename, parent_dir_of_dest_file = get_parnt_dir_and_basename(src_file)
      jsscript = register_jsscript(src_file, basename)

      [jsscript, dest_file, basename, parent_dir_of_dest_file]
    end

    def transform_js_script
      list = @src_dir_pn.children.filter { |file| file.extname == ".js" }
      Util2.count_error_all("Project#transform_js_script", list)  do |src_file|
        jsscript, dest_file, basename, parent_dir_of_dest_file = make_jsscript(src_file)

        jsscript.convertx(dest_file, parent_dir_of_dest_file, basename)
      end
    end

    def transformed_js_list
      Util2.count_error_all("Project#transform_js_script", @jsscripts) do |_, jsscript|
        jsscript.transformed_js_and_src_file
      end
    end

    def make_import_export_file_base(jsscript, parent_dir_of_dest_file, basename)
      status = false
      array = []
      content_parts = jsscript.make_content_part(parent_dir_of_dest_file, basename)

      if content_parts.part1.exist?
        jsscript.analyze(content_parts.part1.load.content)
        array = [jsscript.work_import_file, jsscript.work_export_file]

        status = true
      end
      result = Result.new(status, "Project#make_import_export_file_base")
      result.return_add_array(array)
    end

    def make_import_export_file
      list = @src_dir_pn.children.filter { |file| file.extname == ".js" }
      Util2.count_error_all("Project#make_import_export_file", list) do |src_file|
        jsscript, dest_file, basename, parent_dir_of_dest_file = make_jsscript(src_file)
        make_import_export_file_base(jsscript, parent_dir_of_dest_file, basename)
      end
    end

    def copy_transformed_js_to_src_file_all
      Util2.count_error_all("Project#make_import_export_file", @jsscripts) do |_, jsscript|
        jsscript.copy_transformed_js_to_dest
      end
    end

    def file_list
      array = @jsscripts.map do |_, jsscript|
        [
          jsscript.src_file,
          jsscript.work_head_file,
          jsscript.work_content_file,
          jsscript.work_foot_file,
          jsscript.work_import_file
        ]
      end
      result = Result.new(true, "Project#file_list")
      result.add_return_array(array)
    end
  end
end
