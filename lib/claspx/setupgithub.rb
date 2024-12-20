module Claspx
  class SetupGithub < Setup
    def initialize(project)
      @project = project
      @project_dir_pn = @project.path
    end

    def execute()
      # raise 

      Result.new(true, "SetupGithub#execute")
    end
  end
end