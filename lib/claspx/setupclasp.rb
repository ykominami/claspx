module Claspx
  class SetupClasp < Setup
    def initialize(project, template_clasp_json_pn, template_package_json_pn, template_appsscript_json_pn)
      @project = project
      @project_dir_pn = @project.path
      raise unless template_clasp_json_pn
      @template_clasp_json_pn = template_clasp_json_pn
      @template_package_json_pn = template_package_json_pn
      @template_appsscript_json_pn = template_appsscript_json_pn
    end

    def package_json(template_pn, name, output_file_pn)
      scope = Object.new
      scope.instance_variable_set("@name", name)
      output_file_with_template(template_pn, scope, output_file_pn)

      Result.new(true, "SetupClasp#package_json")
    end

    def template_clasp_json(template_pn, scriptId, root_dir_pn, output_file_pn)
      scope = Object.new
      rootdir = root_dir_pn.to_s

      scope.instance_variable_set("@scriptId", scriptId)
      scope.instance_variable_set("@rootdir", rootdir)
      output_file_with_template(template_pn, scope, output_file_pn)

      Result.new(true, "SetupClasp#template_clasp_json")
    end

    def template_appsscript_json(template_pn, output_file_pn)
      scope = Object.new

      output_file_with_template(template_pn, scope, output_file_pn)

      Result.new(true, "SetupClasp#template_clasp_json")
    end

    def execute()
      root_dir_pn = @project_dir_pn.join(DIST_DIR)
      root_dir_pn.mkpath

      src_dir_pn = @project_dir_pn.join(SRC_DIR)
      src_dir_pn.mkpath unless src_dir_pn.exist?

      script_id = @project.idx
      # p "###### SetupClasp#execute | script_id=#{script_id}"
      output_file_pn = @project_dir_pn.join(CLASP_JSON)
      result = template_clasp_json(@template_clasp_json_pn, script_id, root_dir_pn, output_file_pn)
      return result if result.error?

      output_file_pn = root_dir_pn.join(APPSSCRIPT_JSON)
      result = template_appsscript_json(@template_appsscript_json_pn, output_file_pn)
      return result if result.error?

#      test_data/clasp/etc/dist/appsscript.json
      output_file_pn = @project_dir_pn.join(PACKAGE_JSON)
      result = package_json(@template_package_json_pn, @project.name, output_file_pn)
      return result if result.error?

      Result.new(true, "SetupClasp#execute")
    end
  end
end