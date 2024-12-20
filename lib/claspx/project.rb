module Claspx
  class Project
    attr_accessor :name, :path, :git_repo, :url, :github_url_ssh, 
                  :github_url_https, :idx, :from_remote, :from_local, 
                  :git_cmd, :src_dir, :work_dir,:work_store_dir, 
                  :dist_dir, :jsscripts

    REMOTE = 0
    LOCAL = 1
    def initialize(name, project_dir_pn)
      # 0: retmote 1: local
      @from = [false, false]
      @name = name
      @path = project_dir_pn
      @src_dir = @path.join(SRC_DIR)
      @src_dir.mkpath
      @dist_dir = @path.join(DIST_DIR)
      @dist_dir.mkpath
      @work_dir = @path.join(WORK_DIR)
      @work_dir.mkpath
      @work_store_dir = @path.join(WORK_STORE_DIR)
      @work_store_dir.mkpath

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
      @src_dir.children
    end
    
    def setup()
      all_counter = 0
      error_counter = 0
      error_array = []

      setup_clasp = SetupClasp.new(self, Env.TEMPLATE_CLASP_JSON_PN, Env.TEMPLATE_PACKAGE_JSON_PN, Env.TEMPLATE_APPSSCRIPT_JSON_PN)
      ret = setup_clasp.execute()
      all_counter += 1
      if ret.error?
        error_counter += 1
        error_array.push( [ret2.message, self] )
      end

      setup_git = SetupGit.new(self, Env.TEMPLATE_GITIGNORE_PN)
      ret2 = setup_git.execute()
      all_counter += 1
      if ret2.error?
        error_counter += 1
        error_array.push( [ret2.message, self] )
      end

      setup_github = SetupGithub.new(self)
      ret3 = setup_github.execute
      all_counter += 1
      if ret3.error?
        error_counter += 1
        error_array.push( [ret3.message, self] )
      end

      status = true
      status = false if error_counter > 0
      ret = Result.new(status, "Claspx#setup|all_counter=#{all_counter}|error_counter=#{error_counter}|project=#{@name}")
      ret.add_array(error_array)
    end    

    def get_parnt_dir_and_basename(src_file)
      src_filename = src_file.basename
      dest_file = @dist_dir.join(src_filename)
      basename = src_file.basename(".*")
      parent_dir_of_dest_file = @work_store_dir.join(basename)
      parent_dir_of_dest_file.mkpath

      [dest_file, basename, parent_dir_of_dest_file]
    end

    def register_jsscript(src_file, basename)
      jsscript = Jsscript.new(src_file)

      basename_str = basename.to_s
      @jsscripts[basename_str] = jsscript
    end

    def transform_js_script()
      @src_dir.children.each do |src_file|
        if src_file.extname == ".js"
          dest_file, basename, parent_dir_of_dest_file = get_parnt_dir_and_basename(src_file)
          jsscript = register_jsscript(src_file, basename)

          result = jsscript.convertx(dest_file, parent_dir_of_dest_file, basename)
        end
      end
    end

    def transformed_js_list
      @jsscripts.map{ |k,v|
        v.transformed_js_and_src_file()
      }
    end

    def make_import_export_file
      all_counter = 0
      error_counter = 0
      array = []

      @src_dir.children.each do |src_file|
        status = false
        if src_file.extname == ".js"
          dest_file, basename, parent_dir_of_dest_file = get_parnt_dir_and_basename(src_file)
          jsscript = register_jsscript(src_file, basename)
          content_parts = jsscript.make_content_part(parent_dir_of_dest_file, basename)

          if content_parts.part1.exist?
            jsscript.analyze( content_parts.part1.load.content )
            array << [jsscript.work_import_file, jsscript.work_export_file]

            status = true
          end
        end
        all_counter += 1
        error_counter += 1 unless status

      end
      all_status = true
      all_status = false if error_counter > 0
      result = Result.new(all_status, "Claspx#make_import_export_file")
      result.add_array(array)
    end

    def copy_transformed_js_to_src_file_all
      @jsscripts.map{ |k,v|
        v.copy_transformed_js_to_dest()
      }
    end

    def file_list
      @jsscripts.map{ |_,v|
        [ 
          v.src_file, v.work_head_file,
          v.work_content_file, v.work_foot_file,
          v.work_import_file
        ]
      }
    end
  end
end
