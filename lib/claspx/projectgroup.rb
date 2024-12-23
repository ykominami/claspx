module Claspx
  class ProjectGroup
    attr_reader :all_projects, :local_projects, :remote_projects
    
    def initialize(top_dir, remote_hash)
      @top_dir_pn = Pathname.new(top_dir)

      # ファイルシステムに存在するプロジェクト（リモートと対応するローカルプロジェクト、リモートのみのプロジェクト、ローカルのみのプロジェクト)
      @all_projects = {}
      # ローカルリポジトリプロジェクト
      @local_projects = {}
      # リモートリポジトリプロジェクト
      @remote_projects = {}

      @claspcmd = ClaspCmd.new
      register_remote_bulk(remote_hash)

      @top_dir_pn.each_child do |it|
        register_local(it)
      end
    end

    def setup_all
      Util2.count_error_all("ProjectGroup#setup_all", @all_projects){ |_, project|
        project.setup
      }
    end

    def clasp_clone_all
      Util2.count_error_all("ProjectGroup#clasp_clone_all", @all_projects){ |_, project|
        @claspcmd.clone(project.path.to_s)
      }
    end

    def clasp_pull_all
      Util2.count_error_all("ProjectGroup#clasp_pull_all", @all_projects){ |_, project|
        @claspcmd.pull(project.path.to_s)
      }
    end

    def clasp_push_all(project)
      Util2.count_error_all("ProjectGroup#clasp_push_all", @all_projects){ |_, project|
        @claspcmd.push(project.path.to_s)
      }
    end

    def setup_for_project_on_github
    end

    def print_list_all()
      Loggerxcm.debug %(@local_project_name_list=#{get_local_project_name_list})
      Loggerxcm.debug %(@remote_project_name_list=#{get_remote_project_name_list})

      Loggerxcm.debug "# @remote_only_project_name_list"
      @remote_only_project_name_list = get_remote_only_project
      @remote_only_project_name_list.map { |prname| Loggerxcm.debug %(#{prname} #{prname&.size}) }
      Loggerxcm.debug ""
      Loggerxcm.debug "# @local_only_project_name_list"
      @local_only_project_name_list = get_local_only_project
      @local_only_project_name_list.map { |prname| Loggerxcm.debug %(#{prname} #{prname&.size}) }
    end

    def get_remote_project(name)
      @remote_projects[name]
    end

    def get_local_project(name)
      @local_projects[name]
    end

    def get_or_create_project(name)
      project = @all_projects[name]
      unless project
        project_dir_pn = @top_dir_pn.join(name)
        ret = project_dir_pn.mkpath
  
        project = Project.new(name, project_dir_pn)
        @all_projects[name] = project
      end
      project
    end

    def get_all_projects_name_list
      @all_projects.keys
    end

    def get_both_project_name
      @all_projects.select { |_, project| project.is_all }.keys
    end

    def get_local_only_project
      @local_projects.select { |_, project| project.is_local_only }.keys
    end

    def get_remote_only_project
      @remote_projects.select { |_, project| project.is_remote_only }.keys
    end

    def get_local_project_name_list
      @local_projects.keys
    end

    def get_remote_project_name_list
      @remote_projects.keys
    end

    def register_remote(name, hash)
      project = get_or_create_project(name)
      project.name = name
      project.idx = hash[:idx]
      project.url = hash[:url]
      project.github_url_ssh = hash[:github_url_ssh]
      project.github_url_https = hash[:github_url_https]
      project.set_remote
      @remote_projects[project.name] ||= project
      project
    end

    def register_remote_bulk(hash)
      hash.filter{ |name, value_hash| !name.nil? }.map { |name, value_hash| register_remote(name, value_hash) }
    end

    def register_local(path)
      name = path.basename.to_s
      project = get_or_create_project(name)
      project.name = name
      project.path = path
      project.set_local
      project.git_cmd = GitCmd.new(project.path.to_s)
      @all_projects[name] = project

      @local_projects[project.name] = project unless @local_projects[project.name]
      project
    end
    
    def transformed_js_list_all()
      Util2.count_error_all("ProjectGroup#transformed_js_list", @all_projects){ |_, project|
        project.transformed_js_list()
      }
    end

    def copy_transformed_js_to_src_file_all()
      Util2.count_error_all("ProjectGroup#copy_transformed_js_to_src_file_all", @all_projects){ |_, project|
        project.copy_transformed_js_to_src_file_all()
      }
    end

    def transform_js_script_all()
      Util2.count_error_all("ProjectGroup#transform_js_script_all", @all_projects){ |_, project|
        project.transform_js_script()
      }
    end

    def make_import_export_file_all()
      Util2.count_error_all("ProjectGroup#make_import_export_file_all", @all_projects){ |_, project|
        project.make_import_export_file()
      }
    end
    
    def reform_to_camelcase_js_script_all()
      Util2.count_error_all("ProjectGroup#reform_to_camelcase_js_script_all", @all_projects){ |_, project|
        project.reform_to_camelcase_js_script()
      }
    end

    def file_list
      Util2.count_error_all("ProjectGroup#file_list", @all_projects){ |_, project|
        project.file_list()
      }
    end
  end
end
