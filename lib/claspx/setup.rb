module Claspx
  class Setup
    def output_file_with_template(template_pn, scope, output_file_pn)
      ret = false
      content = Ykxutils::Erubyx.erubi_render_with_template_file(template_pn, scope)
      begin
        File.write(output_file_pn, content)
        ret = true
      rescue => esc
        Loggerxcm.fatal  "output_file_with_template | esc=#{esc}"
      end
      ret
    end
  end
end
