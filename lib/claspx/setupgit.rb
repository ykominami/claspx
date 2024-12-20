module Claspx
  class SetupGit < Setup
    def initialize(project, template_gitignore_pn)
      @project_dir_pn = project.path
      @template_gitignore_pn = template_gitignore_pn
    end

    def template_gitignore(template_pn, output_file_pn)
      scope = Object.new
      output_file_with_template(template_pn, scope, output_file_pn)

      Result.new(true, "SetupGit#template_gitignore")
    end

    def execute
      result = nil
      status = true
      
      output_file_pn = @project_dir_pn.join(GITIGNORE_FILE)
      ret = template_gitignore(@template_gitignore_pn, output_file_pn)
      status = false if ret.error?

      Result.new(status, "SetupGit#execute|git repo|#{@project_dir_pn}")
    end
  end
end