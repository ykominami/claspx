module Claspx
  class WhichCmd < Cmd
    def initialize()
      super("which")
    end

    def which(command)
      cmdline = "#{@name} #{command}"
      result = execute(cmdline)
      p result
      return_value = result.std_out
      result.add_return_value(return_value)
      result
    end
  end
end
